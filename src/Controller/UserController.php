<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfoFormType;
use App\Repository\InvitationRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_home')]
    public function index(InvitationRepository $invitationRepository, SessionRepository $sessionRepository): Response
    {

        $objUser = $this->getUser();

        //Si l'utilisateur n'est pas cconnecter redirection vers la page de connection
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $arrSession = $sessionRepository->findSessionsForUser($objUser);
        $arrInvitation = $invitationRepository->findInvitationsForUser($objUser);


        return $this->render('user/index.html.twig', [
            'arrSession' => $arrSession,
            'arrInvitation' => $arrInvitation,
        ]); 
    }

    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_ADMIN')] 
    public function dashboard(UserRepository $userRepository, Request $request): Response
    {
        $intPage = $request->query->get('page', 1);
        $query = $request->query->get('query', '');

        if(!empty($query)){
          $arrUser =  $userRepository->findAllActiveWithSearch(10, $intPage, $query);
        } else{
            $arrUser = $userRepository->findAllActive(10, $intPage);
        }

        return $this->render('user/dashboard.html.twig', [
            'arrUser' => $arrUser['items'],
            'next'         => $intPage + 1,
            'previous'     => $intPage - 1,
            'allPage'      => $arrUser['pages'],
            'current_page' => $intPage,
            'query' => $query, 
        ]);
        
    }


    #[Route('/user/{id<\d+>}', name: 'app_user_update')]
    #[IsGranted('USER_RIGHT', subject: 'user', message: "Droit insuffisant pour la modification de ce compte !")]
    public function update(User $user, Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager): Response
    {
        $userForm = $this->createForm(UserInfoFormType::class, $user);

        $userForm->handleRequest($request);
        
        if($userForm->isSubmitted() && $userForm->isValid()) {


            $plainPassword = $userForm->get('plainPassword')->getData();

            if($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $photoFile = $userForm->get('photo')->getData();

            if ($photoFile) {
                
                if ($user->getPhoto()) {
                    $oldPhotoPath = $this->getParameter('photos_directory_user') . '/' . $user->getPhoto();
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }

                
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                
                $photoFile->move(
                    $this->getParameter('photos_directory_user'),
                    $newFilename
                );

               
                $user->setPhoto($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été modifié");

            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/form.html.twig', [
            'userForm' => $userForm,
            'user' => $user,
        ]);
    }

    #[Route('/user/delete/{id<\d+>}', name: 'app_user_delete')]
    #[IsGranted('USER_RIGHT', subject: 'user', message: "Droit insuffisant pour la suppression de ce compte !")]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setDeletedAt(new \DateTimeImmutable());
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a été supprimé !");

       return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/user/ban/{id<\d+>}', name: 'app_user_ban')]
    #[IsGranted('ROLE_ADMIN')] 
    #[IsGranted('USER_BAN', subject: 'user', message: "Droit insuffisant pour le bannissement !")]
    public function ban(User $user, EntityManagerInterface $entityManager): Response
    {
        if(is_null($user->getBanAt())){
            $user->setBanAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été banni !");
        } else{
            $user->setBanAt(null);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été débanni !");
        }


        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/user/create', name: 'app_user_create')]
    #[IsGranted('ROLE_ADMIN')] 
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $objUser = new User();

        $userForm = $this->createForm(UserInfoFormType::class, $objUser);

        $userForm->handleRequest($request);
        
        if($userForm->isSubmitted() && $userForm->isValid()) {

         
            $plainPassword = $userForm->get('plainPassword')->getData();

            if(!$plainPassword) {
                $plainPassword = "default_password";
            }
            
            $objUser->setPassword($userPasswordHasher->hashPassword($objUser, $plainPassword));

            
            $objUser->setCreatedAt(new DateTimeImmutable('now'));
            
            $entityManager->persist($objUser);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été créé");

            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/form.html.twig', [
            'userForm' => $userForm,
        ]);
    }

    #[Route('/user/role/{id<\d+>}', name: 'app_user_roles')]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('USER_BAN', subject: 'user', message: "Droit insuffisant pour éditer votre role !")] 
    public function role(User $user, Request $request,  
        EntityManagerInterface $entityManager): Response
        {
            $strFormError = ""; 

            if($request->isMethod('POST')) {

                
                $submittedToken = $request->getPayload()->get('_csrf_token');

                
                if ($this->isCsrfTokenValid('user_role', $submittedToken)) {

                    $arrRoles = []; 

                    
                    if($request->request->get('user-role-admin')) {
                        $arrRoles[] = 'ROLE_ADMIN';
                    }

                    
                    $user->setRoles($arrRoles);
                    $entityManager->flush();

                    $this->addFlash('success', "Les rôles de l'utilisateur ont été modifiés");

                    return $this->redirectToRoute('app_user_dashboard');
                }

                
                $strFormError = "Le jeton de sécurité n'est pas valide. Réessayez ou actualisez la page";
        }

        return $this->render('user/roles.html.twig', [
            'user'      => $user,
            'formError' => $strFormError
        ]);

    }
}