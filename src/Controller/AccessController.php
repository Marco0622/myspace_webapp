<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Session;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/access', name: 'app_access_')]
final class AccessController extends AbstractController
{
    #[Route('/add/{id<\d+>}', name: 'add')]
    public function add(Session $session, EntityManagerInterface $entityManager): Response
    {

        $objAccess = new Access();

        $objAccess->setJoinedAt(new DateTimeImmutable('now'));
        $objAccess->setRole('ROLE_VISITOR');
        $objAccess->setMember($this->getUser());
        $objAccess->setSession($session);

        $entityManager->persist($objAccess);
        $entityManager->flush();

        $this->addFlash('success', 'Bienvenu dans la session : '. $session->getName());

       return $this->redirectToRoute('app_session_home', [
            'id' => $session->getId(),
       ]);
    }
}
