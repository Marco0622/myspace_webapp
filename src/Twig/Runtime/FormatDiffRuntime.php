<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class FormatDiffRuntime implements RuntimeExtensionInterface
{
    

    public function dateDiff(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->days === 0 && $diff->h === 0 && $diff->i === 0) {
            return $diff->s == 1 ? "{$diff->s} seconde" : "{$diff->s} secondes";
        }
        if ($diff->days === 0 && $diff->h === 0) {
            return  $diff->i == 1 ? "{$diff->i} minute" : "{$diff->i} minutes" ;
        }
        if ($diff->days === 0) {
            return $diff->h == 1 ? "{$diff->h} heure" : "{$diff->h} heures";
        }
        if ($diff->days < 30) {
            return $diff->h == 1 ? "{$diff->days} jour" : "{$diff->days} jours";
        }
        if ($diff->m < 12) {
            return "{$diff->m} mois";
        }

        return $diff->y == 1 ? "{$diff->y} an" : "{$diff->y} ans";
    }
}
