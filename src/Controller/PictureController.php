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

#[Route('/picture', name: 'app_picture_')]
final class PictureController extends AbstractController
{
    #[Route('/download/{id<\d+>}', name: 'download', methods: ['POST'])]
    public function download(Session $session, Request $request, PictureManager $pictureManager, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('download_picture', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $objPicture = new Picture();

        $opti = $request->request->get('checkOpti');
        $namePicture = $request->request->get('name');
        $picToAdd = $request->files->get('photo');
        $fileSize = $picToAdd->getSize();

        if(is_null($namePicture) || is_null($picToAdd)){
            $this->addFlash('warning', "Erreur, veuillez réessayer !");
            return $this->redirectToRoute('app_session_gallery', [
                'id' => $session->getId(),
            ]);
        }

        $filename = $pictureManager->upload($picToAdd);

        if($opti === 'optimization'){
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

    #[Route('/delete/{id<\d+>}', name: 'delete', methods: ['POST'])]
    public function delete(Picture $picture, Request $request, PictureManager $pictureManager, EntityManagerInterface $entityManager): Response
    {
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
