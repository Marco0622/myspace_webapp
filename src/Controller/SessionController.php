<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/session', name: 'app_session_')]
final class SessionController extends AbstractController
{
    #[Route('/{id<\d+>}', name: 'home')]
    public function index(): Response
    {
        return $this->render('session/index.html.twig', [
            
        ]);
    }

    #[Route('/page/{id<\d+>}', name: 'page')]
    public function page(): Response
    {
        return $this->render('session/index.html.twig', [
            
        ]);
    }

    #[Route('/manager/{id<\d+>}', name: 'manager')]
    public function manager(): Response
    {
        return $this->render('session/index.html.twig', [
            
        ]);
    }

    #[Route('/gallery/{id<\d+>}', name: 'gallery')]
    public function gallery(): Response
    {
        return $this->render('session/index.html.twig', [
            
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        return $this->render('session/index.html.twig', [
            
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'delete')]
    public function delete(): Response
    {
        return $this->render('session/index.html.twig', [
            
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/blocked/{id<\d+>}', name: 'blocked')]
    public function blocked(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('session_blockage', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }


        if($session->isBlocked()){
            $session->setIsBlocked(false);
            $entityManager->flush();

            $this->addFlash('success', "La session est débloquée !");
        } else{
            $session->setIsBlocked(true);
            $entityManager->flush();

            $this->addFlash('success', "La session est bloquée !");
        }

        return $this->redirectToRoute('app_dashboard_sessions');
    }
}
