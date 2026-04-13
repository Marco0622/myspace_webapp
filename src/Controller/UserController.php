<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfoFormType;
use App\Repository\InvitationRepository;
use App\Repository\ReportRepository;
use App\Repository\SessionRepository;
use App\Service\CodeInvitationGenerator;
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
    public function index(InvitationRepository $invitationRepository, SessionRepository $sessionRepository, ReportRepository $reportRepository): Response
    {

        $objUser = $this->getUser();

        //Si l'utilisateur n'est pas cconnecter redirection vers la page de connection
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $arrSession = $sessionRepository->findSessionsForUser($objUser);
        $arrInvitation = $invitationRepository->findInvitationsForUser($objUser);
        $arrReport = $reportRepository->findReportOfUser($objUser);

       

        return $this->render('user/index.html.twig', [
            'arrSession' => $arrSession,
            'arrInvitation' => $arrInvitation,
            'arrReport' => $arrReport,
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
            $user->setUpdatedAt(new DateTimeImmutable('now'));
            $entityManager->flush();

            if($user !== $this->getUser()){
                 $this->addFlash('success', "L'utilisateur a été modifié");
                return $this->redirectToRoute('app_dashboard_users');
            } else{
                $this->addFlash('success', "Votre profil a été modifié !");
                return $this->redirectToRoute('app_user_home');
            }
        }

        return $this->render('user/form.html.twig', [
            'userForm' => $userForm,
            'user' => $user,
        ]);
    }

    #[Route('/user/delete/{id<\d+>}', name: 'app_user_delete')]
    #[IsGranted('USER_RIGHT', subject: 'user', message: "Droit insuffisant pour la suppression de ce compte !")]
    public function delete(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $user->setDeletedAt(new \DateTimeImmutable());
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a été supprimé !");

        return $this->redirectToRoute('app_dashboard_users');
    }

    #[Route('/user/ban/{id<\d+>}', name: 'app_user_ban')]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('USER_BAN', subject: 'user', message: "Droit insuffisant pour le bannissement !")]
    public function ban(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('ban', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        if(is_null($user->getBanAt())){
            $user->setBanAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été banni !");
        } else{
            $user->setBanAt(null);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur a été débanni !");
        }

        return $this->redirectToRoute('app_dashboard_users');
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

            return $this->redirectToRoute('app_dashboard_users');
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

                    return $this->redirectToRoute('app_dashboard_users');
                }

                
                $strFormError = "Le jeton de sécurité n'est pas valide. Réessayez ou actualisez la page";
        }

        return $this->render('user/roles.html.twig', [
            'user'      => $user,
            'formError' => $strFormError
        ]);
    }

    #[Route('/user/new-code/{id<\d+>}', name: 'app_user_code', methods: ['POST'])]
    public function codeInvitation(User $user, Request $request, CodeInvitationGenerator $codeInvitation, EntityManagerInterface $entityManager): Response
    {

        if (!$this->isCsrfTokenValid('new_code', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        if($user === $this->getUser()){

            $code = $codeInvitation->newCode();

            $user->setCode($code);
            $entityManager->flush();
            $this->addFlash('success', "Un nouveau code a été généré !");
        } else{
            $this->addFlash('danger', "Vous n'avez pas les droits !");
        }
    
        return $this->redirectToRoute('app_user_update',[
            'id' => $user->getId(),
        ]);
    }

}