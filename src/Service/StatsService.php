<?php

namespace App\Service;

use App\Repository\InvitationRepository;
use App\Repository\NodeRepository;
use App\Repository\PageRepository;
use App\Repository\PictureRepository;
use App\Repository\ReportRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;

/**
 * Service utilisé principalement pour l'affichage des statistiques dans le dashboard.
 */
class StatsService
{
    /**
     * Injection des différents repositories pour le calcul des statistiques.
     */
    public function __construct(
        private UserRepository          $userRepository,
        private SessionRepository       $sessionRepository,
        private PictureRepository      $pictureRepository,
        private NodeRepository          $nodeRepository,
        private ReportRepository        $reportRepository,
        private InvitationRepository    $invitationRepository,
        private PageRepository          $pageRepository,
       
    ) {}

    /**
     * Récupère le décompte total de chaque entité majeure.
     * 
     * @return array tableau associatif contenant les statistiques globales.
     */
    public function getDashboardStats(): array
    {
        return [
            'users'         => $this->userRepository->count([]),
            'sessions'      => $this->sessionRepository->count([]),
            'pictures'      => $this->pictureRepository->count([]),
            'nodes'         => $this->nodeRepository->count([]),
            'reports'       => $this->reportRepository->count([]),
            'invitations'   => $this->invitationRepository->count([]),
            'pages'   => $this->pageRepository->count([]),
        ];
    }
}