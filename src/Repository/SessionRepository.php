<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * Cherche les sessions auxquelles un utilisateur appartient, avec les accès et membres associés.
     * 
     * @param $user l'utilisateur en sessions.
     */
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

    /**
     * Prépare la requête de recherche des sessions pour la pagination du dashboard.
     * 
     * @param $query recherche de l'utilisateur sur le nom de la session.
     */
    public function sessionQuerybuilderForPaginator(string $query): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.sessionAccesses', 'all_accesses')
            ->addSelect('all_accesses')
            ->leftJoin('all_accesses.member', 'members')
            ->addSelect('members')
            ->where('LOWER(s.name) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('s.created_at', 'DESC');      
    }

    /**
     * Récupère une session complète avec ses relations (accès, invitations, membres, pages) par son ID.
     * 
     * @param $id l'identifiant de la session.
     */
    public function findSessionWithRelations(int $id): ?Session
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.sessionAccesses', 'all_accesses')
            ->addSelect('all_accesses')
            ->leftJoin('s.sessionInvitations', 'all_invitation')
            ->addSelect('all_invitation')
            ->leftJoin('all_accesses.member', 'members')
            ->addSelect('members')
            ->leftJoin('s.sessionPages', 'pages')
            ->addSelect('pages')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
