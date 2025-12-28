<?php

namespace App\Repository;

use App\Entity\Burger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BurgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Burger::class);
    }

    public function findNonArchived(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.isArchived = :archived')
            ->setParameter('archived', false)
            ->orderBy('b.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
