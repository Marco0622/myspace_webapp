<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Session;
use App\Repository\AccessRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gère les droits d'accès et les permissions des utilisateurs au sein des sessions.
 */

#[Route('/access', name: 'app_access_')]
final class AccessController extends AbstractController
{
   /**
    * Ajoute l'utilisateur actuel à une session après qu'une invitation a été acceptée.
    *
    * @param Session $session
    * @param EntityManagerInterface $entityManager
    * @return Response
    */
   #[Route('/add/{id<\d+>}', name: 'add', methods: ['GET'])]
   #[IsGranted('IS_RECEIVER', subject: 'session', message: "Vous devez avoir était invité !")]
   public function add(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository): Response
   {

      $objAccess = new Access();

      $objAccess->setJoinedAt(new DateTimeImmutable('now'));
      $objAccess->setRole('ROLE_VISITOR');
      $objAccess->setMember($this->getUser());
      $objAccess->setSession($session);

      $entityManager->persist($objAccess);
      $entityManager->flush();

      $this->addFlash('success', 'Bienvenu dans la session : ' . $session->getName());

      return $this->redirectToRoute('app_session_home', [
         'id' => $session->getId(),
      ]);
   }

   /**
    * Exclut un membre d'une session accéssible uniquement par le propriétaire
    * de la session il ne peux pas s'auto-exclure.
    *
    * @param Session $session
    * @param EntityManagerInterface $entityManager
    * @param AccessRepository $accessRepository
    * @param Request $request
    * @return Response
    */
   #[Route('/remove/{id<\d+>}', name: 'remove', methods: ['POST'])]
   #[IsGranted('IS_OWNER', subject: 'session', message: "Droit insuffisant  Seul le propriétaire de la session peux exclure un utilisateur !")]
   public function remove(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository, Request $request): Response
   {
      if (!$this->isCsrfTokenValid('remove_access', $request->request->get('_token'))) {
         throw $this->createAccessDeniedException('Token CSRF invalide.');
      }

      $idUserToRemove = $request->request->get('userToRemove');

      $objUserToRemove = $accessRepository->findOneBy([
         'session' => $session,
         'member' => $idUserToRemove,
      ]);

      if (is_null($objUserToRemove) || $this->getUser() == $objUserToRemove) {
         $this->addFlash('warning', 'Une erreur est survenue !');
         return $this->redirectToRoute('app_user_home');
      }

      $entityManager->remove($objUserToRemove);
      $entityManager->flush();

      $this->addFlash('success', "L'utilisateur a été exclu !");


      return $this->redirectToRoute('app_session_home', [
         'id' => $session->getId(),
      ]);
   }

   /**
    * Permet de quitter une session à condition de ne pas être propriétaire.
    *
    * @param Session $session
    * @param EntityManagerInterface $entityManager
    * @param AccessRepository $accessRepository
    * @param Request $request
    * @return Response
    */
   #[Route('/leave/{id<\d+>}', name: 'leave', methods: ['POST'])]
   #[IsGranted('REMOVE_ACCESS', subject: 'session', message: "Vous devez étre menbre de la session et ne pas étre le propriétaire !")]
   public function leave(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository, Request $request): Response
   {
      if (!$this->isCsrfTokenValid('leave_session', $request->request->get('inv_token'))) {
         throw $this->createAccessDeniedException('Token CSRF invalide.');
      }

      $objUserAccess = $accessRepository->findOneBy([
         'session' => $session,
         'member' => $this->getUser(),
      ]);

      if (is_null($objUserAccess)) {
         $this->addFlash('warning', 'Une erreur est survenue !');
         return $this->redirectToRoute('app_session_home', [
            'id' => $session->getId(),
         ]);
      }

      $entityManager->remove($objUserAccess);
      $entityManager->flush();

      $this->addFlash('success', "Vous avez quitté la session !");


      return $this->redirectToRoute('app_user_home');
   }

   /**
    * Permet de modifier le role d'un menbre d'une session condition d'être propriétaire de la session.
    *
    * @param Session $session
    * @param EntityManagerInterface $entityManager
    * @param AccessRepository $accessRepository
    * @param Request $request
    * @return Response
    */
   #[Route('/role/{id<\d+>}', name: 'role', methods: ['POST'])]
   #[IsGranted('IS_OWNER', subject: 'session', message: "Droit insuffisant Seul le propriétaire de la session peux exclure un utilisateur !")]
   public function role(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository, Request $request): Response
   {
      if (!$this->isCsrfTokenValid('role_access', $request->request->get('_token'))) {
         throw $this->createAccessDeniedException('Token CSRF invalide.');
      }

      $idUserToUpdate = $request->request->get('userToUpdate');
      $role = $request->request->get('role');

      $objUserToUpdate = $accessRepository->findOneBy([
         'session' => $session,
         'member' => $idUserToUpdate,
      ]);

      if (is_null($idUserToUpdate) || $this->getUser() == $idUserToUpdate || is_null($role)) {
         $this->addFlash('warning', 'Une erreur est survenue !');
         return $this->redirectToRoute('app_session_home', [
            'id' => $session->getId(),
         ]);
      }

      if ($role === 'visitor') {
         $objUserToUpdate->setRole('ROLE_VISITOR');
      }
      if ($role === 'editor') {
         $objUserToUpdate->setRole('ROLE_EDITOR');
      }

      $entityManager->flush();

      $this->addFlash('success', "Le rôle a été modifié !");


      return $this->redirectToRoute('app_session_home', [
         'id' => $session->getId(),
      ]);
   }
}
