<?php

namespace App\Repository;

use App\Entity\Complement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complement::class);
    }

    public function findNonArchived(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isArchived = :archived')
            ->setParameter('archived', false)
            ->orderBy('c.type', 'ASC')
            ->addOrderBy('c.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.type = :type')
            ->andWhere('c.isArchived = :archived')
            ->setParameter('type', $type)
            ->setParameter('archived', false)
            ->orderBy('c.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
