<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Page;
use App\Entity\Session;
use App\Entity\Storage;
use App\Repository\AccessRepository;
use App\Repository\NodeRepository;
use App\Repository\PictureRepository;
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
    public function index(int $id, SessionRepository $sessionRepository, AccessRepository $accessRepository): Response
    {
        $session = $sessionRepository->findSessionWithRelations($id);

        $this->denyAccessUnlessGranted('IS_VISITOR_SESSION', $session);

        $objOwner = $accessRepository->findOneBy([
            'role' => 'ROLE_OWNER',
            'session' => $session
        ]);

        return $this->render('session/index.html.twig', [
            'session' => $session,
            'owner' => $objOwner->getMember(),
        ]);
    }

    #[Route('/page/{id<\d+>}', name: 'page')]
    public function page(Page $page, SessionRepository $sessionRepository): Response
    {
        $session = $sessionRepository->findSessionWithRelations($page->getSession()->getId());

        $this->denyAccessUnlessGranted('IS_VISITOR_SESSION', $session);

        return $this->render('session/page.html.twig', [
            'session' => $session,
            'page' => $page,
        ]);
    }

    #[Route('/manager/{id<\d+>}', name: 'manager')]
    public function manager(int $id, Request $request, SessionRepository $sessionRepository, NodeRepository $nodeRepository): Response
    {
        $query = $request->query->get('query', '');
        $filter = $request->query->get('filter', '');
        $folder = $request->query->get('folder', 0);

        $session = $sessionRepository->findSessionWithRelations($id);
        $arrNodes = $nodeRepository->findAllNodeForManager($id, $filter, $query, $folder);

        $this->denyAccessUnlessGranted('IS_VISITOR_SESSION', $session);

        if ($folder > 0) {
            $folder = $nodeRepository->findOneBy(['id' => $folder]);
        }

        return $this->render('session/manager.html.twig', [
            'session' => $session,
            'arrNodes' => $arrNodes,
            'query' => $query,
            'filter' => $filter,
            'folder' => $folder,
        ]);
    }

    #[Route('/gallery/{id<\d+>}', name: 'gallery')]
    public function gallery(int $id, PictureRepository $pictureRepository, SessionRepository $sessionRepository, Request $request): Response
    {
        $query = $request->query->get('query', '');

        $session = $sessionRepository->findSessionWithRelations($id);
        $arrPictures = $pictureRepository->findAllPictureForGallery($id, $query);

        $this->denyAccessUnlessGranted('IS_VISITOR_SESSION', $session);


        return $this->render('session/gallery.html.twig', [
            'session' => $session,
            'arrPicture' => $arrPictures,
            'query' => $query,
        ]);
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

    #[Route('/delete/{id<\d+>}', name: 'delete', methods: ['POST'])]
    public function delete(Session $session, Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $this->denyAccessUnlessGranted('IS_OWNER_SESSION', $session);

        if (!$this->isCsrfTokenValid('delete_session', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $confirm = $request->request->get('deleteConfirm');

        if ($confirm != "Je confirme") {
            $this->addFlash('danger', "Erreur lors de la tentative de suppression !");
            return $this->redirectToRoute('app_session_home', [
                'id' => $session->getId(),
            ]);
        }

        $entityManager->remove($session);
        $entityManager->flush();

        $this->addFlash('success', "La session a été supprimé !");
        return $this->redirectToRoute('app_user_home');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/blocked/{id<\d+>}', name: 'blocked', methods: ['POST'])]
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
