<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livraisons')]
class LivraisonController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private UserRepository $userRepository,
        private ZoneRepository $zoneRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_livraison_index')]
    public function index(): Response
    {
        // Récupérer toutes les commandes à livrer (non affectées à un livreur)
        $commandesALivrer = $this->commandeRepository->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->leftJoin('c.zone', 'zone')
            ->addSelect('client', 'zone')
            ->where('c.modeConsommation = :livraison')
            ->andWhere('c.livreurId IS NULL')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('livraison', 'LIVRAISON')
            ->setParameter('etats', ['VALIDEE', 'EN_PREPARATION', 'TERMINEE'])
            ->orderBy('zone.nom', 'ASC')
            ->addOrderBy('c.dateCommande', 'ASC')
            ->getQuery()
            ->getResult();

        // Regrouper par zone
        $commandesParZone = [];
        foreach ($commandesALivrer as $commande) {
            $zoneNom = $commande->getZone() ? $commande->getZone()->getNom() : 'Sans zone';
            $zoneId = $commande->getZone() ? $commande->getZone()->getId() : null;

            if (!isset($commandesParZone[$zoneNom])) {
                $commandesParZone[$zoneNom] = [
                    'zone' => $commande->getZone(),
                    'zoneId' => $zoneId,
                    'commandes' => []
                ];
            }

            $commandesParZone[$zoneNom]['commandes'][] = $commande;
        }

        // Récupérer tous les livreurs disponibles
        $livreurs = $this->userRepository->findLivreursDisponibles();

        return $this->render('livraison/index.html.twig', [
            'commandesParZone' => $commandesParZone,
            'livreurs' => $livreurs,
            'totalCommandes' => count($commandesALivrer)
        ]);
    }

    #[Route('/affecter', name: 'app_livraison_affecter', methods: ['POST'])]
    public function affecter(Request $request): Response
    {
        // Récupérer les données du formulaire
        $commandeIds = $request->request->all('commandes');
        $livreurId = $request->request->get('livreur_id');

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('affecter_livraison', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_livraison_index');
        }

        // Vérifier qu'on a bien des commandes et un livreur
        if (empty($commandeIds) || !$livreurId) {
            $this->addFlash('error', 'Veuillez sélectionner au moins une commande et un livreur.');
            return $this->redirectToRoute('app_livraison_index');
        }

        // Vérifier que le livreur existe et est disponible
        $livreur = $this->userRepository->find($livreurId);
        if (!$livreur || $livreur->getRole() !== 'LIVREUR') {
            $this->addFlash('error', 'Livreur invalide.');
            return $this->redirectToRoute('app_livraison_index');
        }

        // Affecter les commandes au livreur
        $compteur = 0;
        foreach ($commandeIds as $commandeId) {
            $commande = $this->commandeRepository->find($commandeId);

            if ($commande && $commande->getModeConsommation() === 'LIVRAISON' && !$commande->getLivreurId()) {
                $commande->setLivreur($livreur);
                $commande->setEtat('EN_LIVRAISON');
                $compteur++;
            }
        }

        $this->entityManager->flush();

        $this->addFlash('success', "{$compteur} commande(s) affectée(s) à {$livreur->getPrenom()} {$livreur->getNom()}.");

        return $this->redirectToRoute('app_livraison_index');
    }

    #[Route('/historique', name: 'app_livraison_historique')]
    public function historique(): Response
    {
        // Récupérer toutes les commandes en livraison ou livrées
        $commandesEnCours = $this->commandeRepository->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->leftJoin('c.zone', 'zone')
            ->leftJoin('c.livreur', 'livreur')
            ->addSelect('client', 'zone', 'livreur')
            ->where('c.modeConsommation = :livraison')
            ->andWhere('c.livreurId IS NOT NULL')
            ->setParameter('livraison', 'LIVRAISON')
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('livraison/historique.html.twig', [
            'commandes' => $commandesEnCours
        ]);
    }

    #[Route('/{id}/retirer-livreur', name: 'app_livraison_retirer', methods: ['POST'])]
    public function retirerLivreur(int $id, Request $request): Response
    {
        $commande = $this->commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('retirer_livreur_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_livraison_historique');
        }

        // Vérifier que la commande est bien en livraison
        if ($commande->getEtat() !== 'EN_LIVRAISON') {
            $this->addFlash('error', 'Cette commande n\'est plus en livraison.');
            return $this->redirectToRoute('app_livraison_historique');
        }

        // Retirer le livreur
        $livreurNom = $commande->getLivreur()->getPrenom() . ' ' . $commande->getLivreur()->getNom();
        $commande->setLivreur(null);
        $commande->setEtat('TERMINEE');

        $this->entityManager->flush();

        $this->addFlash('success', "Livreur {$livreurNom} retiré de la commande #{$commande->getNumeroCommande()}.");

        return $this->redirectToRoute('app_livraison_index');
    }
}
