<?php

namespace App\Repository;

use App\Entity\Zone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('z')
            ->where('z.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('z.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
