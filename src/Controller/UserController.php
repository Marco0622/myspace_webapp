<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfoFormType;
use App\Repository\InvitationRepository;
use App\Repository\ReportRepository;
use App\Repository\SessionRepository;
use App\Repository\StorageRepository;
use App\Service\CodeInvitationGenerator;
use App\Service\PictureManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gère le profil utilisateur et l'administration des comptes.
 * Inclut la gestion du profil personnel, la modération (ban, suppression) et l'attribution des rôles.
 */
final class UserController extends AbstractController
{   
    /**
     * Page d'accueil du profil utilisateur affichant ses sessions, invitations et signalements.
     * 
     * @param InvitationRepository $invitationRepository
     * @param SessionRepository $sessionRepository
     * @param ReportRepository $reportRepository
     * @param StorageRepository $storageRepository
     * @return Response
     */
    #[Route('/', name: 'app_user_home')]
    public function index(InvitationRepository $invitationRepository, 
    SessionRepository $sessionRepository, ReportRepository $reportRepository,
    StorageRepository $storageRepository): Response
    {

        $objUser = $this->getUser();
        $arrStorage = $storageRepository->findAll();

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
            'arrStorage' => $arrStorage,
        ]);
    }

    /**
     * Met à jour les informations d'un utilisateur (mot de passe, photo, profil).
     * 
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param PictureManager $pictureManager
     * @return Response
     */
    #[Route('/user/{id<\d+>}', name: 'app_user_update')]
    #[IsGranted('USER_RIGHT', subject: 'user', message: "Droit insuffisant pour la modification de ce compte !")]
    public function update(
        User $user,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        PictureManager $pictureManager
    ): Response {
        $userForm = $this->createForm(UserInfoFormType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {


            $plainPassword = $userForm->get('plainPassword')->getData();

            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $photoFile = $userForm->get('photo')->getData();

            if ($photoFile) {
                $newFilename = $pictureManager->upload($photoFile, $user->getPhoto());
                $pictureManager->resize($newFilename, 250, 250);
                $user->setPhoto($newFilename);
            }

            $user->setUpdatedAt(new DateTimeImmutable('now'));
            $entityManager->flush();

            if ($user != $this->getUser()) {
                $this->addFlash('success', "L'utilisateur a été modifié");
                return $this->redirectToRoute('app_dashboard_users');
            } else {
                $this->addFlash('success', "Votre profil a été modifié !");
                return $this->redirectToRoute('app_user_home');
            }
        }

        return $this->render('user/form.html.twig', [
            'userForm' => $userForm,
            'user' => $user,
        ]);
    }

    /**
     * Effectue un soft delete d'un compte utilisateur via la propriété deletedAt.
     * 
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
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

    /**
     * Alterne l'état de bannissement d'un utilisateur (Action réservée aux administrateurs).
     * 
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/user/ban/{id<\d+>}', name: 'app_user_ban')]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('USER_BAN', subject: 'user', message: "Droit insuffisant pour le bannissement !")]
    public function ban(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('ban', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        if (is_null($user->getBanAt())) {
            $user->setBanAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été banni !");
        } else {
            $user->setBanAt(null);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur a été débanni !");
        }

        return $this->redirectToRoute('app_dashboard_users');
    }

    /**
     * Permet à un administrateur de créer manuellement un nouvel utilisateur.
     * 
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/create', name: 'app_user_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $objUser = new User();

        $userForm = $this->createForm(UserInfoFormType::class, $objUser);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {


            $plainPassword = $userForm->get('plainPassword')->getData();

            if (!$plainPassword) {
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

    /**
     * Gère l'attribution des rôles (notamment ROLE_ADMIN) pour un utilisateur spécifique.
     * 
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/role/{id<\d+>}', name: 'app_user_roles')]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('USER_BAN', subject: 'user', message: "Droit insuffisant pour éditer votre role !")]
    public function role(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $strFormError = "";

        if ($request->isMethod('POST')) {


            $submittedToken = $request->getPayload()->get('_csrf_token');


            if ($this->isCsrfTokenValid('user_role', $submittedToken)) {

                $arrRoles = [];


                if ($request->request->get('user-role-admin')) {
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

    /**
     * Génère un nouveau code d'invitation unique pour l'utilisateur.
     * 
     * @param User $user
     * @param Request $request
     * @param CodeInvitationGenerator $codeInvitation
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/new-code/{id<\d+>}', name: 'app_user_code', methods: ['POST'])]
    public function codeInvitation(User $user, Request $request, CodeInvitationGenerator $codeInvitation, EntityManagerInterface $entityManager): Response
    {

        if (!$this->isCsrfTokenValid('new_code', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        if ($user === $this->getUser()) {

            $code = $codeInvitation->newCode();

            $user->setCode($code);
            $entityManager->flush();
            $this->addFlash('success', "Un nouveau code a été généré !");
        } else {
            $this->addFlash('danger', "Vous n'avez pas les droits !");
        }

        return $this->redirectToRoute('app_user_update', [
            'id' => $user->getId(),
        ]);
    }

    /**
     * Supprime la photo de profil d'un utilisateur et nettoie le stockage physique.
     * 
     * @param User $user
     * @param Request $request
     * @param PictureManager $pictureManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/delete-picture/{id<\d+>}', name: 'app_user_picture', methods: ['POST'])]
    #[IsGranted('USER_RIGHT', subject: 'user', message: "Droit insuffisant pour la suppression de la photo !")]
    public function deletePicture(User $user, Request $request, PictureManager $pictureManager, EntityManagerInterface $entityManager): Response
    {

        if (!$this->isCsrfTokenValid('delete_picture', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $pictureManager->delete($user->getPhoto());

        $user->setPhoto(null);
        $entityManager->flush();
        $this->addFlash('success', "La photo a été supprimée !");

        return $this->redirectToRoute('app_user_update', [
            'id' => $user->getId(),
        ]);
    }
}
