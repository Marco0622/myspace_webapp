<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class FormatDiffRuntime implements RuntimeExtensionInterface
{
    
    /**
     * Filtre twig permet d'avoir l'écart entre la date actuelle et une date ultérieure.
     * 
     * @param \DateTimeInterface $date date à comparer avec la date actuelle.
     */
    public function dateDiff(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->days === 0 && $diff->h === 0 && $diff->i === 0 && $diff->m == 0 && $diff->y == 0) {
            return $diff->s == 1 ? "{$diff->s} seconde" : "{$diff->s} secondes";
        }
        if ($diff->days === 0 && $diff->h === 0 && $diff->m == 0 && $diff->y == 0) {
            return  $diff->i == 1 ? "{$diff->i} minute" : "{$diff->i} minutes" ;
        }
        if ($diff->days === 0 && $diff->m == 0 && $diff->y == 0) {
            return $diff->h == 1 ? "{$diff->h} heure" : "{$diff->h} heures";
        }
        if ($diff->days < 30 && $diff->m == 0) {
            return $diff->h == 1 ? "{$diff->days} jour" : "{$diff->days} jours";
        }
        if ($diff->m < 12 && $diff->y == 0) {
            return "{$diff->m} mois";
        }

        return $diff->y == 1 ? "{$diff->y} an" : "{$diff->y} ans";
    }
}
