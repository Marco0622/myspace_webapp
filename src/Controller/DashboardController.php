<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/dashboard', name: 'app_dashboard_')]
final class DashboardController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(StatsService $statsService): Response
    {
        $arrStats = $statsService->getDashboardStats();

        return $this->render('dashboard/index.html.twig', [
            'arrStats' => $arrStats,
        ]);
    }


    #[Route('/users', name: 'users')]
    public function users(UserRepository $userRepository, Request $request): Response
    {
        $intPage = $request->query->get('page', 1);
        $query = $request->query->get('query', '');

        
        $arrUser =  $userRepository->findAllActiveWithSearch(10, $intPage, $query);
        

        return $this->render('dashboard/dashboard_users.html.twig', [
            'arrUser' => $arrUser['items'],
            'next'         => $intPage + 1,
            'previous'     => $intPage - 1,
            'allPage'      => $arrUser['pages'],
            'current_page' => $intPage,
            'query' => $query, 
        ]);
    }

    #[Route('/sessions', name: 'sessions')]
    public function sessions(Request $request ,SessionRepository $sessionRepository): Response
    {
         $intPage = $request->query->get('page', 1);
        $query = $request->query->get('query', '');

        $arrSession = $sessionRepository->findSessionsForAdminWithSeach(10, $intPage, $query);

        return $this->render('dashboard/dashboard_sessions.html.twig', [
            'arrSession' => $arrSession['items'],
            'next'         => $intPage + 1,
            'previous'     => $intPage - 1,
            'allPage'      => $arrSession['pages'],
            'current_page' => $intPage,
            'query' => $query, 
        ]);
    }

    #[Route('/reports', name: 'reports')]
    public function reports(ReportRepository $reportRepository): Response
    {

        $arrReport = $reportRepository->allReportForAdmin();

        return $this->render('dashboard/dashboard_reports.html.twig', [
            'arrReport' => $arrReport,
        ]);
    }
}
