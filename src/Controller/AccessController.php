<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Session;
use App\Repository\AccessRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/access', name: 'app_access_')]
final class AccessController extends AbstractController
{
    #[Route('/add/{id<\d+>}', name: 'add', methods: ['GET'])]
    public function add(Session $session, EntityManagerInterface $entityManager, AccessRepository $accessRepository): Response
    {
        $existingOwner = $accessRepository->findOneBy([
            'session' => $session,
            'role' => 'ROLE_OWNER'
        ]);

        $objAccess = new Access();

        $objAccess->setJoinedAt(new DateTimeImmutable('now'));
        if(is_null($existingOwner)){
            $objAccess->setRole('ROLE_VISITOR');
        } else{
            $objAccess->setRole('ROLE_OWNER');
        }
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
