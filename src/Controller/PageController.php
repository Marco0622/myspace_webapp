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

/**
 * Gère les opérations liées aux pages d'une session : création, suppression et sauvegarde du contenu.
 * Accès contrôlé par le droit IS_EDITOR_SESSION sur la session propriétaire.
 */
#[Route('/page', name: 'app_page_')]
final class PageController extends AbstractController
{
    /**
     * Création d'une nouvelle page associée à une session.
     * 
     * @param Session $session
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PageRepository $pageRepository
     * @return Response
     */
    #[Route('/create/{id<\d+>}', name: 'create')]
    public function create(Session $session, Request $request, EntityManagerInterface $entityManager, PageRepository $pageRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $session);

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

        $this->addFlash('success', 'La page a était créé !');

        return $this->redirectToRoute('app_session_page', [
            'id' => $objPage->getId(),
        ]);
    }

    /**
     * Supprime une page spécifique et redirige vers l'accueil de la session.
     * 
     * @param Page $page
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/delete/{id<\d+>}', name: 'delete')]
    public function delete(Page $page, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $page->getSession());

        if (!$this->isCsrfTokenValid('delete_page', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $session = $page->getSession();
        $entityManager->remove($page);
        $entityManager->flush();

        $this->addFlash('success', 'La page a était supprimé !');

        return $this->redirectToRoute('app_session_home', [
            'id' => $session->getId(),
        ]);
    }

    /**
     * Sauvegarde le contenu d'une page via une requête JSON (AJAX).
     * 
     * @param Page $page
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/save/{id<\d+>}', name: 'save', methods: ['POST'])]
    public function save(Page $page, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $page->getSession());

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Payload invalide'], 400);
        }

        $content = $data['content'] ?? null;
        $token = $data['_token'] ?? null;
        if (!$this->isCsrfTokenValid('save_page', $token)) {
            return $this->json(['error' => 'Token CSRF invalide'], 403);
        }

        $page->setContent($content);
        $page->setEditedAt(new DateTimeImmutable('now'));
        $page->setEditedBy($this->getUser());

        $entityManager->flush();


        return $this->json(['success' => true]);
    }
}
