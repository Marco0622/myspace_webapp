<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Session;
use App\Entity\Storage;
use App\Repository\SessionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/session', name: 'app_session_')]
final class SessionController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    #[Route('/{id<\d+>}', name: 'home')]
    public function index(int $id, SessionRepository $sessionRepository): Response
    {
       
        $session = $sessionRepository->findSessionWithRelations($id);
        
        return $this->render('session/index.html.twig', [
            'session' => $session,
        ]);
    }

    #[Route('/page/{id<\d+>}', name: 'page')]
    public function page(): Response
    {
        return $this->render('session/index.html.twig', []);
    }

    #[Route('/manager/{id<\d+>}', name: 'manager')]
    public function manager(): Response
    {
        return $this->render('session/index.html.twig', []);
    }

    #[Route('/gallery/{id<\d+>}', name: 'gallery')]
    public function gallery(): Response
    {
        return $this->render('session/index.html.twig', []);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('session_create', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        try {
            $name = $request->request->get('name');
            $storageId = $request->request->get('storage_id');
            $objStorage = $entityManager->getRepository(Storage::class)->find($storageId);

            if (empty($name) || strlen($name) < 2) {
                $this->addFlash('warning', "Le nom de la session est obligatoire (2 caractères min) !");
                return $this->redirectToRoute('app_user_home');
            }
            $objSession = new Session();
            $objSession->setCreatedAt(new DateTimeImmutable('now'));
            $objSession->setIsBlocked(false);
            $objSession->setName($name);
            $objSession->setStorage($objStorage);

            
            $objAccess = new Access();
            $objAccess->setJoinedAt(new DateTimeImmutable('now'));
            $objAccess->setRole('ROLE_OWNER');
            $objAccess->setMember($this->getUser());
            $objAccess->setSession($objSession);

            $entityManager->persist($objSession);
            $entityManager->persist($objAccess);
            $entityManager->flush();


            return $this->redirectToRoute('app_session_home', [
                'id' => $objSession->getId(),
            ]);
        } catch (\Exception $e) {

            $this->logger->error("Erreur lors de la création d'une session", [
                'id' => $this->getUser(),
                'msg' => $e->getMessage()
            ]);


            $this->addFlash('danger', "L'action n'a pas pu être effectuée.");
            return $this->redirectToRoute('app_user_home');
        }
    }

    #[Route('/delete/{id<\d+>}', name: 'delete')]
    public function delete(): Response
    {
        return $this->render('session/index.html.twig', []);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/blocked/{id<\d+>}', name: 'blocked')]
    public function blocked(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('session_blockage', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }


        if ($session->isBlocked()) {
            $session->setIsBlocked(false);
            $entityManager->flush();

            $this->addFlash('success', "La session est débloquée !");
        } else {
            $session->setIsBlocked(true);
            $entityManager->flush();

            $this->addFlash('success', "La session est bloquée !");
        }

        return $this->redirectToRoute('app_dashboard_sessions');
    }
}
