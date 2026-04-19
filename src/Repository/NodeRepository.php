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

    /**
     * Cherche les fichiers/dossiers d'une session.
     * 
     * @param $id de la session.
     * @param $filter filter sélectionné par l'utilisateur.
     * @param $query recherche de l'utilisateur.
     * @param $parent id du parent.
     */
    public function findAllNodeForManager(int $id, string $filter = '', string $query = '', int $parent = 0, string $type = ''): array
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->innerJoin('n.session', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id);

        if (!empty($query)) {
            $queryBuilder->andWhere('LOWER(n.name )LIKE LOWER(:q)')
                ->orWhere('LOWER(n.type )LIKE LOWER(:q)')
                ->setParameter('q', '%' . $query . '%');
        }

        if ($parent > 0) {
            $queryBuilder->andWhere('n.parent = :parent')
                ->setParameter('parent', $parent);
        } else {
            $queryBuilder->andWhere('n.parent IS NULL');
        }

        if(!empty($type)){
            $queryBuilder->andWhere('n.type = :type')
                ->setParameter('type', $type);
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

    /**
     * Retourne les types/extensions de fichiers distincts présents dans un dossier ou à la racine.
     *
     * @param int $sessionId
     * @param int|null $folderId
     * @return array
     */
    public function findTypeForFilter(int $sessionId, mixed $folderId = null): array
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->select('DISTINCT n.type')
            ->where('n.session = :sessionId')
            ->setParameter('sessionId', $sessionId);

        if ($folderId > 0) {
            $queryBuilder->andWhere('n.parent = :folder')
                ->setParameter('folder', $folderId);
        } else {
            $queryBuilder->andWhere('n.parent IS NULL');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
