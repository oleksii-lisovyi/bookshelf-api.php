<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function getPagination(int $limit, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.lastname')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function findByText(string $t): array
    {
        if (empty($t)) {
            return [];
        }

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Author::class, 'a');

        $sql = <<<END
            SELECT a.*
            FROM author AS a
            WHERE to_tsvector(a.firstname || ' ' || coalesce(a.middlename, '') || ' ' || a.lastname) @@ to_tsquery(?)
            ORDER BY a.lastname;
END;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $t);

        return $query->getResult();
    }
}
