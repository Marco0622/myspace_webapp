<?php

namespace App\Service;

use App\Repository\NodeRepository;
use App\Repository\PictureRepository;

/**
 * Service utilisé pour les calcules du stockage concernant les sessions.
 */
class StorageService
{
    /**
     * Injection des différents repositories pour le calcul des statistiques.
     */
    public function __construct(
        private PictureRepository      $pictureRepository,
        private NodeRepository          $nodeRepository,
    ) {}

    /**
     * Calcule la valeur total du stockage Go utiliser par une session.
     * 
     * @return array 
     */
    public function storageUseByOneSession(object $session): float
    {
        $storageSize = $this->pictureRepository->getTotalSizePicture($session->getId());
        $storageSize += $this->nodeRepository->getTotalSizeNode($session->getId());


        $result = $storageSize / pow(1024, 3);

        if ($result > 0 && $result < 0.01) {
            $result = 0.01;
        }

        return number_format($result, 2, '.', ' ');
    }

    /**
     * Vérifie si une session a suffisamment de place pour accueillir un nouveau fichier.
     *
     * @param object $session      La session concernée.
     * @param float  $maxStorageGo Le stockage maximum autorisé pour la session (en Go).
     * @param int    $fileSizeOctet La taille du fichier à ajouter (en octets).
     *
     * @return bool True si la session a la place, false sinon.
     */
    public function sessionHasEnoughStorage(object $session, float $maxStorageGo, int $fileSizeOctet): bool
    {
        $currentStorageOctet = $this->pictureRepository->getTotalSizePicture($session->getId());
        $currentStorageOctet += $this->nodeRepository->getTotalSizeNode($session->getId());

        $maxStorageOctet = $maxStorageGo * pow(1024, 3);

        return ($currentStorageOctet + $fileSizeOctet) <= $maxStorageOctet;
    }
}
