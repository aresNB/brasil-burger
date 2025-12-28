<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    public function findProduitsLesPlusVendusToday(): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0);
        $endOfDay = (clone $today)->setTime(23, 59, 59);

        return $this->createQueryBuilder('lc')
            ->select('lc.typeProduit, lc.burgerId, lc.menuId, SUM(lc.quantite) as total')
            ->join('lc.commande', 'c')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->setParameter('etats', ['VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE'])
            ->groupBy('lc.typeProduit, lc.burgerId, lc.menuId')
            ->orderBy('total', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
