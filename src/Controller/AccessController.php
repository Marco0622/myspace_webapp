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
 * @todo sécuriser la création des accés avec un voter 
 */

#[Route('/access', name: 'app_access_')]
final class AccessController extends AbstractController
{


    #[Route('/add/{id<\d+>}', name: 'add', methods: ['GET'])]
    #[IsGranted('IS_RECEIVER', subject: 'session', message: "Vous devez avoir était invité !")]
    public function add(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository): Response
    {
       
        $objAccess = new Access();

        $objAccess->setJoinedAt(new DateTimeImmutable('now'));
        $objAccess->setRole('ROLE_OWNER');
        $objAccess->setMember($this->getUser());
        $objAccess->setSession($session);

        $entityManager->persist($objAccess);
        $entityManager->flush();

        $this->addFlash('success', 'Bienvenu dans la session : '. $session->getName());

       return $this->redirectToRoute('app_session_home', [
            'id' => $session->getId(),
       ]);
    }


    #[Route('/remove/{id<\d+>}', name: 'remove', methods: ['POST'])]
    #[IsGranted('IS_OWNER', subject: 'session', message: "Droit insuffisant  Seul le propriétaire de la session peux exclure un utilisateur !")]
    public function remove(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository): Response
    {
        

       return $this->redirectToRoute('app_user_home');
    }

     
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

      if(is_null($objUserAccess)){
         $this->addFlash('warning', 'Une erreur est survenue !');
         return $this->redirectToRoute('app_user_home');
      }

      $entityManager->remove($objUserAccess);
      $entityManager->flush();

      $this->addFlash('success', "Vous avez quitté la session !");


       return $this->redirectToRoute('app_user_home');
    }


    #[Route('/role/{id<\d+>}', name: 'role', methods: ['POST'])]
    #[IsGranted('IS_OWNER', subject: 'session', message: "Droit insuffisant Seul le propriétaire de la session peux exclure un utilisateur !")]
    public function role(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository): Response
    {
        

       return $this->redirectToRoute('app_user_home');
    }
}
