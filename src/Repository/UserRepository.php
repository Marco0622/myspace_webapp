<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function userCreateQueryBuilderPaginator(string $query): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->where('(LOWER(u.firstname) LIKE LOWER(:q) 
                        OR LOWER(u.name) LIKE LOWER(:q)
                        OR LOWER(CONCAT(u.firstname, \' \', u.name)) LIKE LOWER(:q)
                        OR LOWER(CONCAT(u.name, \' \', u.firstname)) LIKE LOWER(:q)) 
                        AND u.deleted_at IS NULL')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('u.created_at', 'DESC');
            

    }
}
