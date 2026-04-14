<?php

namespace App\Controller;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @todo faire un voter pour la gestion des invitation
 */

#[Route('/invitation', name: 'app_invitation_')]
final class InvitationController extends AbstractController
{
    #[Route('/decline/{id<\d+>}', name: 'decline')]
    public function decline(Invitation $invitation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('decline_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $invitation->setResponce(false);
        $entityManager->flush();

        $this->addFlash('success', "Vous avez décliné l'invitation !");

        return $this->redirectToRoute('app_user_home');
    }

    #[Route('/accept/{id<\d+>}', name: 'accept')]
    public function accept(Invitation $invitation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('accept_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $invitation->setResponce(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_access_add', [
            'id' => $invitation->getSession()->getId(),
        ]);
    }

    #[Route('/cancel/{id<\d+>}', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('invitation/index.html.twig', [
            
        ]);
    }

    /*#[Route('/create/{id<\d+>}', name: 'create')]
    public function create(): Response
    {
        
    }*/

}
