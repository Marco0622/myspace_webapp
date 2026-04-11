<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function findReportOfUser($user){
        return $this->createQueryBuilder('r')
            ->where('r.author = :user')
            ->setParameter('user', $user)
            ->orderBy('r.send_at', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function allReportForAdmin(){
        return $this->createQueryBuilder('r')
            ->innerJoin('r.author', 'a')
            ->addSelect('a')
            ->andWhere('r.response IS NULL')
            ->andWhere('a.deleted_at IS NULL')
            ->andWhere('a.ban_at IS NULL')
            ->orderBy('r.send_at', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
