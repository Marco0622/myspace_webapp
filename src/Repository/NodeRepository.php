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

    public function findAllNodeForManager(int $id, string $filter = '', string $query): array
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->innerJoin('n.session', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id);

        if (!empty($query)) {
            $queryBuilder->andWhere('LOWER(n.name )LIKE LOWER(:q)')
                ->setParameter('q', '%' . $query . '%');
        }

        switch ($filter) {
            case 'A-Z':
                $queryBuilder->orderBy('LOWER(n.name)', 'ASC');
                break;
            case 'Z-A':
                $queryBuilder->orderBy('LOWER(n.name)', 'DESC');
                break;
            case 'recent':
                $queryBuilder->orderBy('n.addAt', 'DESC');
                break;
            case 'old':
                $queryBuilder->orderBy('n.addAt', 'ASC');
                break;
            default:
                $queryBuilder->orderBy('n.addAt', 'DESC');
                break;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
