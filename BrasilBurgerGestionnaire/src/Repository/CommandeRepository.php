<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findByEtat(string $etat): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.etat = :etat')
            ->setParameter('etat', $etat)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDate(\DateTime $date): array
    {
        $startOfDay = (clone $date)->setTime(0, 0, 0);
        $endOfDay = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('c')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCommandesEnCoursToday(): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0);
        $endOfDay = (clone $today)->setTime(23, 59, 59);

        return $this->createQueryBuilder('c')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->setParameter('etats', ['EN_ATTENTE', 'VALIDEE', 'EN_PREPARATION', 'EN_LIVRAISON'])
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCommandesValideesToday(): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0);
        $endOfDay = (clone $today)->setTime(23, 59, 59);

        return $this->createQueryBuilder('c')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->andWhere('c.etat = :etat')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->setParameter('etat', 'VALIDEE')
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getRecettesJournalieres(\DateTime $date = null): float
    {
        if ($date === null) {
            $date = new \DateTime();
        }

        $startOfDay = (clone $date)->setTime(0, 0, 0);
        $endOfDay = (clone $date)->setTime(23, 59, 59);

        $result = $this->createQueryBuilder('c')
            ->select('SUM(c.montantTotal) as total')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->setParameter('etats', ['VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE'])
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float)$result : 0;
    }

    public function findCommandesAnnuleesToday(): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0);
        $endOfDay = (clone $today)->setTime(23, 59, 59);

        return $this->createQueryBuilder('c')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->andWhere('c.etat = :etat')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->setParameter('etat', 'ANNULEE')
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Rechercher des commandes avec filtres multiples
     */
    public function findWithFilters(
        ?string $etat = null,
        ?\DateTime $date = null,
        ?int $clientId = null,
        ?string $typeProduit = null
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->leftJoin('c.zone', 'zone')
            ->leftJoin('c.livreur', 'livreur')
            ->addSelect('client', 'zone', 'livreur')
            ->orderBy('c.dateCommande', 'DESC');

        if ($etat) {
            $qb->andWhere('c.etat = :etat')
                ->setParameter('etat', $etat);
        }

        if ($date) {
            $startOfDay = (clone $date)->setTime(0, 0, 0);
            $endOfDay = (clone $date)->setTime(23, 59, 59);

            $qb->andWhere('c.dateCommande BETWEEN :start AND :end')
                ->setParameter('start', $startOfDay)
                ->setParameter('end', $endOfDay);
        }

        if ($clientId) {
            $qb->andWhere('c.clientId = :clientId')
                ->setParameter('clientId', $clientId);
        }

        if ($typeProduit) {
            $qb->leftJoin('c.lignesCommande', 'lc')
                ->andWhere('lc.typeProduit = :typeProduit')
                ->setParameter('typeProduit', $typeProduit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Compter les commandes par état
     */
    public function countByEtat(): array
    {
        $results = $this->createQueryBuilder('c')
            ->select('c.etat, COUNT(c.id) as total')
            ->groupBy('c.etat')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($results as $result) {
            $counts[$result['etat']] = (int)$result['total'];
        }

        return $counts;
    }
}
