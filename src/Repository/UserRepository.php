<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findAllActive(int $number, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.deleted_at IS NULL')
            ->orderBy('u.created_at', 'DESC');
            

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

    public function findAllActiveWithSearch(int $number, int $page, string $query): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('LOWER(u.firstname) LIKE LOWER(:q) OR LOWER(u.name) LIKE LOWER(:q) AND u.deleted_at IS NULL')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('u.created_at', 'DESC');
            

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
