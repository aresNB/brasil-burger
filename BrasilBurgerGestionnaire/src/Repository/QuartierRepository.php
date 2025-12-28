<?php

namespace App\Repository;

use App\Entity\Quartier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuartierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quartier::class);
    }

    public function findByZone(int $zoneId): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.zoneId = :zoneId')
            ->setParameter('zoneId', $zoneId)
            ->orderBy('q.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
