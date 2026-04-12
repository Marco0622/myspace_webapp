<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findSessionsForUser($user): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.sessionAccesses', 'a')
            ->andWhere('a.member = :user')
            ->setParameter('user', $user)
            ->leftJoin('s.sessionAccesses', 'all_accesses')
            ->addSelect('all_accesses')
            ->leftJoin('all_accesses.member', 'members')
            ->addSelect('members')
            ->orderBy('s.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findSessionsForAdminWithSeach(int $number, int $page, $query): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.sessionAccesses', 'all_accesses')
            ->addSelect('all_accesses')
            ->leftJoin('all_accesses.member', 'members')
            ->addSelect('members')
            ->where('LOWER(s.name) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('s.created_at', 'DESC');

        $paginator = new Paginator($queryBuilder->getQuery());

        $intItemCount = count($paginator);

        $intPageCount = ceil($intItemCount / $number);

        $paginator->getQuery()
            ->setFirstResult($number * $page - $number)
            ->setMaxResults($number);
        
         return [
            'count' => $intItemCount,
            'pages' => $intPageCount,
            'items' => $paginator
        ];
            
    }
}
