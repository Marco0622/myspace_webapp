<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Session;
use App\Repository\PageRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/page', name: 'app_page_')]
final class PageController extends AbstractController
{
    #[Route('/create/{id<\d+>}', name: 'create')]
    public function create(Session $session, Request $request, EntityManagerInterface $entityManager, PageRepository $pageRepository): Response
    {
        if (!$this->isCsrfTokenValid('page_create', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $name = $request->request->get('pageName');

        $objPage = new Page();

        $objPage->setName($name);
        $objPage->setCreatedBy($this->getUser());
        $objPage->setCreatedAt(new DateTimeImmutable('now'));
        $objPage->setSession($session);

        $entityManager->persist($objPage);
        $entityManager->flush();

        return $this->redirectToRoute('app_session_page', [
            'id' => $session->getId(),
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'delete')]
    public function delete(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/save/{id<\d+>}', name: 'save')]
    public function save(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

}
