<?php

namespace App\Controller;

use App\Entity\Node;
use App\Entity\Session;
use App\Service\FileManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/node', name: 'app_node_')]
final class NodeController extends AbstractController
{
    #[Route('/upload/{id<\d+>}', name: 'upload', methods: ['POST'])]
    public function upload(Session $session, Request $request, FileManager $fileManager, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('upload_node', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $nodeName  = $request->request->get('name');
        $fileToAdd = $request->files->get('file');

        if (is_null($nodeName) || is_null($fileToAdd)) {
            $this->addFlash('warning', 'Erreur, veuillez réessayer !');
            return $this->redirectToRoute('app_session_manager', [
                'id' => $session->getId()
            ]);
        }

        $type = $fileToAdd->getMimeType();
        $size = $fileToAdd->getSize();
        $extension = $fileToAdd->guessExtension();
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

        $entityManager->persist($node);
        $entityManager->flush();

        $this->addFlash('success', 'Le fichier a été téléchargé avec succès !');

        return $this->redirectToRoute('app_session_manager', [
            'id' => $session->getId()
        ]);
    }

    #[Route('/delete-file/{id<\d+>}', name: 'delete_file', methods: ['POST'])]
    public function delete(Node $node, Request $request, FileManager $fileManager, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_node', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $sessionId = $node->getSession()->getId();

        $fileManager->remove($node->getPath());

        $entityManager->remove($node);
        $entityManager->flush();

        $this->addFlash('success', "Le fichier a été supprimée avec succès !");

        return $this->redirectToRoute('app_session_manager', [
            'id' => $sessionId
        ]);
    }
}
