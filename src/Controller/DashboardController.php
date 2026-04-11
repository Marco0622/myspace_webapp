<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/dashboard', name: 'app_dashboard_')]
final class DashboardController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }


    #[Route('/users', name: 'users')]
    public function users(UserRepository $userRepository, Request $request): Response
    {
        $intPage = $request->query->get('page', 1);
        $query = $request->query->get('query', '');

        if(!empty($query)){
          $arrUser =  $userRepository->findAllActiveWithSearch(10, $intPage, $query);
        } else{
            $arrUser = $userRepository->findAllActive(10, $intPage);
        }

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
    public function sessions(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
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
