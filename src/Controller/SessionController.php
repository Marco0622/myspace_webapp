<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/session', name: 'app_session_')]
final class SessionController extends AbstractController
{
    #[Route('/', name: 'home')]
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
}
