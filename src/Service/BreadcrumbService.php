<?php

namespace App\Service;

use App\Entity\Node;

/**
 * Service pour la gestion des fil d'ariane.
 */
class BreadcrumbService
{
    /**
     * Retourne le fil d'ariane du fichier dans lequel se situe l'utilisateur.
     * 
     * @param object $currentNode Fichier dans lequel est l'utilisateur.
     * @return array Tableau d'objet contenant les informations des parents.
     */
    public function buildBreadcrumb(Node $currentNode): array
    {
        $breadcrumb = [];

        while ($currentNode !== null) {
            array_unshift($breadcrumb, $currentNode);
            $currentNode = $currentNode->getParent();
        }

        return $breadcrumb;
    }
}
