<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invitation', name: 'app_invitation_')]
final class InvitationController extends AbstractController
{
    #[Route('/decline/{id<\d+>}', name: 'decline')]
    public function decline(): Response
    {
        return $this->render('invitation/index.html.twig', [
            
        ]);
    }


    #[Route('/accept/{id<\d+>}', name: 'accept')]
    public function accept(): Response
    {
        return $this->render('invitation/index.html.twig', [
            
        ]);
    }

    #[Route('/cancel/{id<\d+>}', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('invitation/index.html.twig', [
            
        ]);
    }

}
