<?php

namespace App\Repository;

use App\Entity\Node;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Node>
 */
class NodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Node::class);
    }

    /*public function findAllPictureForGallery(int $id, string $filter = '', string $query): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.session', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id);
        if($filter === 'A-Z'){
            $queryBuilder->orderBy('p.name', 'DESC');
        }
        if($filter === 'Z-A'){
            $queryBuilder->orderBy('p.name', 'ASC');
        }
        if($filter === 'recent'){
            $queryBuilder->orderBy('p.created_at', 'DESC');
        }
        if($filter === 'old'){
            $queryBuilder->orderBy('p.created_at', 'ASC');
        }

       return $queryBuilder->getQuery()
            ->getResult();

       
    }*/
}
