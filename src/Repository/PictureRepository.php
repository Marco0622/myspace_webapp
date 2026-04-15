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

    public function findAllPictureForGallery(int $id): array
    {
        return $this->createQueryBuilder('p')
                    ->innerJoin('p.session', 's')
                    ->where('s.id = :id')
                    ->setParameter('id', $id)
                    ->orderBy('p.created_at', 'ASC')
                    ->getQuery()
                    ->getResult();
    }               
}
