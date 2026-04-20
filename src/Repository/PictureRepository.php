<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Picture>
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    /**
     * Cherche les images de la galerie d'une session.
     * 
     * @param $id de la session.
     * @param $query recherche de l'utilisateur.
     * @return array
     */
    public function findAllPictureForGallery(int $id, string $query): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.session', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id);
            if(!empty($query)){
                $queryBuilder->andWhere('LOWER(p.name) LIKE LOWER(:q)')
                    ->setParameter('q','%'. $query . '%');
            }

          return  $queryBuilder->getQuery()
                ->getResult();
    }
}
