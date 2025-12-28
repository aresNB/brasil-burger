<?php

namespace App\Service;

use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;

class StatistiqueService
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private LigneCommandeRepository $ligneCommandeRepository
    ) {}

    /**
     * Obtenir toutes les statistiques du jour
     */
    public function getStatistiquesJour(\DateTime $date = null): array
    {
        if ($date === null) {
            $date = new \DateTime();
        }

        return [
            'commandesEnCours' => $this->getCommandesEnCoursCount($date),
            'commandesValidees' => $this->getCommandesValideesCount($date),
            'recettesJournalieres' => $this->getRecettesJournalieres($date),
            'commandesAnnulees' => $this->getCommandesAnnuleesCount($date),
            'produitsLesPlusVendus' => $this->getProduitsLesPlusVendus($date),
        ];
    }

    /**
     * Nombre de commandes en cours aujourd'hui
     */
    public function getCommandesEnCoursCount(\DateTime $date = null): int
    {
        return count($this->commandeRepository->findCommandesEnCoursToday());
    }

    /**
     * Nombre de commandes validées aujourd'hui
     */
    public function getCommandesValideesCount(\DateTime $date = null): int
    {
        return count($this->commandeRepository->findCommandesValideesToday());
    }

    /**
     * Recettes journalières
     */
    public function getRecettesJournalieres(\DateTime $date = null): float
    {
        return $this->commandeRepository->getRecettesJournalieres($date);
    }

    /**
     * Nombre de commandes annulées aujourd'hui
     */
    public function getCommandesAnnuleesCount(\DateTime $date = null): int
    {
        return count($this->commandeRepository->findCommandesAnnuleesToday());
    }

    /**
     * Top 5 des produits les plus vendus aujourd'hui
     */
    public function getProduitsLesPlusVendus(\DateTime $date = null): array
    {
        $resultats = $this->ligneCommandeRepository->findProduitsLesPlusVendusToday();

        // Limiter aux 5 premiers
        return array_slice($resultats, 0, 5);
    }
}
