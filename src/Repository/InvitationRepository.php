<?php

namespace App\Repository;

use App\Entity\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invitation>
 */
class InvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    public function findInvitationsForUser($user){
        return $this->createQueryBuilder('i')
            ->where('i.receiver_id = :user')
            ->setParameter('user', $user)
            ->orderBy('i.send_at', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
