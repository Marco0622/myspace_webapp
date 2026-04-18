<?php

namespace App\Controller;

use App\Entity\Node;
use App\Entity\Session;
use App\Repository\NodeRepository;
use App\Service\FileManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

        $fileToAdd = $request->files->get('file');
        $parentId   = $request->request->get('parent_id');
        $objNodeParent = $nodeRepository->find($parentId) ?? null;
        $nodeName = pathinfo($fileToAdd->getClientOriginalName(), PATHINFO_FILENAME);

        if (is_null($nodeName) || is_null($fileToAdd)) {
            $this->addFlash('warning', 'Erreur, veuillez réessayer !');
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId()
            ]);
        }

        $size = $fileToAdd->getSize();
        $extension = $fileToAdd->getClientOriginalExtension();
        $filename = $fileManager->upload($fileToAdd);


        $node = new Node();
        $node->setName($nodeName);
        $node->setPath($filename);
        $node->setSize($size);
        $node->setType($extension);
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

    /**
     * Méthode permettant renommer du champ name de la table pictures
     * 
     * @param Picture $picture fichier à télécharger
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/rename/{id<\d+>}', name: 'rename', methods: ['POST'])]
    public function rename(Node $node, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $node->getSession());

        if (!$this->isCsrfTokenValid('rename_node', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $newName = trim($request->request->get('name', ''));

        if ($newName === '') {
            $this->addFlash('warning', 'Le nom ne peut pas être vide.');
            return $this->redirectToRoute('app_session_manager', [
                'id' => $node->getSession()->getId()
            ]);
        }

        $node->setName($newName);
        $entityManager->flush();

        $this->addFlash('success', "Le fichier a été renommée avec succès !");

        return $this->redirectToRoute('app_session_manager', [
            'id' => $node->getSession()->getId()
        ]);
    }

    /**
     * Méthode permettant le téléchargement de maniére sécuriser
     * 
     * @param Picture $picture fichier à télécharger
     */
    #[Route('/download/{id<\d+>}', name: 'download', methods: ['GET'])]
    public function download(Node $node): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $node->getSession());

        if ($node->getType() === 'folder') {
            $this->addFlash('warning', "Les dossier ne sont pas téléchargable !");
            return $this->redirectToRoute('app_session_manager', [
                'id' => $node->getSession()->getId()
            ]);
        }

        $filepath = $this->getParameter('files_session_directory') . '/' . $node->getPath();

        if (!file_exists($filepath)) {
            $this->addFlash('warning', "Fichier introuvable.");
            return $this->redirectToRoute('app_session_manager', [
                'id' => $node->getSession()->getId()
            ]);
        }

        $filename = $node->getName() . '.' . $node->getType();
        // Nom du fichier / code 200 requête réussie
        return new BinaryFileResponse($filepath, 200, [ 
            // donne un mimeType que le navigateur ne peux pas éxecuter
            'Content-Type'           => 'application/octet-stream',
            // Oblige le navigateur a le télécharger
            'Content-Disposition'    => 'attachment; filename="' . $filename, 
            //bloque le navigateur pour ne pas qu'il devine le mimiType
            'X-Content-Type-Options' => 'nosniff', 
        ]);
    }
}
