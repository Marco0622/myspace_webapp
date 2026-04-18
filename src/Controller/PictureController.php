<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Session;
use App\Service\PictureManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gère les médias visuels de la galerie : upload (avec option d'optimisation) et suppression.
 * L'accès est contrôlé par le droit IS_EDITOR_SESSION sur la session propriétaire.
 */
#[Route('/picture', name: 'app_picture_')]
final class PictureController extends AbstractController
{
    /**
     * Gère l'upload d'une image, sa potentielle optimisation et son enregistrement en base de données.
     * 
     * @param Session $session
     * @param Request $request
     * @param PictureManager $pictureManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/download/{id<\d+>}', name: 'download', methods: ['POST'])]
    public function download(Session $session, Request $request, PictureManager $pictureManager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $session);

        if (!$this->isCsrfTokenValid('download_picture', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $objPicture = new Picture();

        $opti = $request->request->get('checkOpti');
        $namePicture = $request->request->get('name');
        $picToAdd = $request->files->get('photo');
        $fileSize = $picToAdd->getSize();

        if (is_null($namePicture) || is_null($picToAdd)) {
            $this->addFlash('warning', "Erreur, veuillez réessayer !");
            return $this->redirectToRoute('app_session_gallery', [
                'id' => $session->getId(),
            ]);
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
        $extension = strtolower($picToAdd->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            $this->addFlash('warning', "Format invalide. Seuls les formats PNG, JPG et WEBP sont acceptés.");
            return $this->redirectToRoute('app_session_gallery', [
                'id' => $session->getId(),
            ]);
        }

        $filename = $pictureManager->upload($picToAdd);

        if ($opti === 'optimization') {
            $pictureManager->resize($filename, 400, 400, true);
        }

        $objPicture->setAddBy($this->getUser());
        $objPicture->setCreatedAt(new DateTimeImmutable('now'));
        $objPicture->setName($namePicture);
        $objPicture->setPath($filename);
        $objPicture->setSession($session);
        $objPicture->setSize($fileSize);

        $entityManager->persist($objPicture);
        $entityManager->flush();

        $this->addFlash('success', "L'image a été téléchargée avec succès !");

        return $this->redirectToRoute('app_session_gallery', [
            'id' => $session->getId(),
        ]);
    }

    /**
     * Supprime physiquement le fichier image et retire son enregistrement en base de données.
     * 
     * @param Picture $picture
     * @param Request $request
     * @param PictureManager $pictureManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/delete/{id<\d+>}', name: 'delete', methods: ['POST'])]
    public function delete(Picture $picture, Request $request, PictureManager $pictureManager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_EDITOR_SESSION', $picture->getSession());

        if (!$this->isCsrfTokenValid('delete_picture', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $sessionId =  $picture->getSession();

        $pictureManager->delete($picture->getPath());

        $entityManager->remove($picture);
        $entityManager->flush();

        $this->addFlash('success', "L'image a été supprimer avec succès !");

        return $this->redirectToRoute('app_session_gallery', [
            'id' => $sessionId->getId(),
        ]);
    }
}
