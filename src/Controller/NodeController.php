<?php

namespace App\Controller;

use App\Entity\Node;
use App\Entity\Session;
use App\Repository\NodeRepository;
use App\Service\FileManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gère les opérations sur les nœuds (fichiers et dossiers) : upload, création et suppression.
 * L'accès est contrôlé par le droit IS_EDITOR_SESSION sur la session concernée.
 */
#[Route('/node', name: 'app_node_')]
final class NodeController extends AbstractController
{   
    /**
     * Gère l'upload d'un fichier dans une session ou un dossier spécifique.
     * 
     * @param Session $session
     * @param Request $request
     * @param FileManager $fileManager
     * @param EntityManagerInterface $entityManager
     * @param NodeRepository $nodeRepository
     * @return Response
     */
    #[Route('/upload/{id<\d+>}', name: 'upload', methods: ['POST'])]
    public function upload(
        Session $session,
        Request $request,
        FileManager $fileManager,
        EntityManagerInterface $entityManager,
        NodeRepository $nodeRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $session);

        if (!$this->isCsrfTokenValid('upload_node', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $nodeName  = $request->request->get('name');
        $fileToAdd = $request->files->get('file');
        $parentId   = $request->request->get('parent_id');
        $objNodeParent = $nodeRepository->find($parentId) ?? null;

        if (is_null($nodeName) || is_null($fileToAdd)) {
            $this->addFlash('warning', 'Erreur, veuillez réessayer !');
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId()
            ]);
        }

        $type = $fileToAdd->getMimeType();
        $size = $fileToAdd->getSize();
        $extension = $fileToAdd->getClientOriginalExtension();
        $filename = $fileManager->upload($fileToAdd);
        $nodeName .= '.' . $extension;


        $node = new Node();
        $node->setName($nodeName);
        $node->setPath($filename);
        $node->setSize($size);
        $node->setType($type);
        $node->setSession($session);
        $node->setAddAt(new DateTimeImmutable('now'));
        $node->setAddBy($this->getUser());
        $node->setParent($objNodeParent);

        $entityManager->persist($node);
        $entityManager->flush();

        $this->addFlash('success', 'Le fichier a été téléchargé avec succès !');
        if (is_null($objNodeParent)) {
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId(),
            ]);
        } else {
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId(),
                'folder' => $objNodeParent->getId() ?? 0,
            ]);
        }
        
    }

    /**
     * Supprime un fichier physiquement et son entrée en base de données, ou un dossier.
     * 
     * @param Node $node
     * @param Request $request
     * @param FileManager $fileManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/delete-file/{id<\d+>}', name: 'delete_file', methods: ['POST'])]
    public function delete(Node $node, Request $request, FileManager $fileManager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $node->getSession());

        if (!$this->isCsrfTokenValid('delete_node', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $sessionId = $node->getSession()->getId();

        if ($node->getType() !== 'folder') {
            $fileManager->remove($node->getPath());
        }

        $entityManager->remove($node);
        $entityManager->flush();

        $this->addFlash('success', "Le fichier a été supprimée avec succès !");

        return $this->redirectToRoute('app_session_manager', [
            'id' => $sessionId
        ]);
    }

    /**
     * Crée un nouveau dossier au sein d'une session ou d'un dossier parent.
     * 
     * @param Session $session
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param NodeRepository $nodeRepository
     * @return Response
     */
    #[Route('/create/{id<\d+>}', name: 'folder_create', methods: ['POST'])]
    public function createFolder(
        Session $session,
        Request $request,
        EntityManagerInterface $entityManager,
        NodeRepository $nodeRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $session);

        if (!$this->isCsrfTokenValid('create_folder', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $folderName = trim($request->request->get('name', ''));
        $parentId   = $request->request->get('parent_id');
        $objNodeParent = $nodeRepository->find($parentId) ?? null;

        if ($folderName === '') {
            $this->addFlash('warning', 'Le nom du dossier ne peut pas être vide.');
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId()
            ]);
        }

        $folder = new Node();
        $folder->setName($folderName);
        $folder->setType('folder');
        $folder->setSize(0);
        $folder->setPath('');
        $folder->setSession($session);
        $folder->setAddBy($this->getUser());
        $folder->setAddAt(new DateTimeImmutable('now'));
        $folder->setParent($objNodeParent);

        $entityManager->persist($folder);
        $entityManager->flush();

        $this->addFlash('success', 'Le dossier a été créé avec succès !');

        if (is_null($objNodeParent)) {
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId(),
            ]);
        } else {
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId(),
                'folder' => $objNodeParent->getId() ?? 0,
            ]);
        }
    }
}
