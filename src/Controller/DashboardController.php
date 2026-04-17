<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use App\Service\StatsService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gère l'affichage des statistiques et la consultation des données sensibles.
 * Accès strictement réservé aux utilisateurs possédant le rôle ROLE_ADMIN.
 */

#[Route('/dashboard', name: 'app_dashboard_')]
final class DashboardController extends AbstractController
{
    /**
     * Accueil du dashboard.
     * 
     * @param StatsService $statsService Class service permettent l'affichage de statistiques.
     * @return response
     */

    #[Route('/', name: 'home')]
    public function index(StatsService $statsService): Response
    {
        $arrStats = $statsService->getDashboardStats();

        return $this->render('dashboard/index.html.twig', [
            'arrStats' => $arrStats,
        ]);
    }

    /**
     * Liste des utilisateurs avec recherche et pagination.
     * 
     * @param UserRepository 
     * @param Request $request
     * @param PaginatorInterface 
     * @return Response
     */
    #[Route('/users', name: 'users')]
    public function users(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('query', '');


        $query =  $userRepository->userCreateQueryBuilderPaginator($search);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('perPage', 10)
        );


        return $this->render('dashboard/dashboard_users.html.twig', [
            'pagination' => $pagination,
            'query' => $search,
        ]);
    }

    /**
     * Liste des sessions avec recherche et pagination.
     * 
     * @param Request $request
     * @param SessionRepository $sessionRepository 
     * @param PaginatorInterface $paginator 
     * @return Response
     */
    #[Route('/sessions', name: 'sessions')]
    public function sessions(Request $request, SessionRepository $sessionRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('query', '');

        $query = $sessionRepository->sessionQuerybuilderForPaginator($search);


        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('perPage', 10)
        );


        return $this->render('dashboard/dashboard_sessions.html.twig', [
            'pagination' => $pagination,
            'query' => $search,
        ]);
    }

    /**
     * Consultation des signalements des 5 derniers signalement pour l'administration.
     * 
     * @param ReportRepository $reportRepository
     * @return Response
     */
    #[Route('/reports', name: 'reports')]
    public function reports(ReportRepository $reportRepository): Response
    {

        $arrReport = $reportRepository->allReportForAdmin();

        return $this->render('dashboard/dashboard_reports.html.twig', [
            'arrReport' => $arrReport,
        ]);
    }
}
