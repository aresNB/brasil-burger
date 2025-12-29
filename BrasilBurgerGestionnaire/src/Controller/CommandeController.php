<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commandes')]
class CommandeController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_commande_index')]
    public function index(Request $request): Response
    {
        // Récupérer les paramètres de filtre
        $etat = $request->query->get('etat');
        $date = $request->query->get('date');
        $clientId = $request->query->get('client');

        // Construire la requête avec filtres
        $queryBuilder = $this->commandeRepository->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->leftJoin('c.zone', 'zone')
            ->leftJoin('c.livreur', 'livreur')
            ->addSelect('client', 'zone', 'livreur')
            ->orderBy('c.dateCommande', 'DESC');

        // Filtre par état
        if ($etat) {
            $queryBuilder->andWhere('c.etat = :etat')
                ->setParameter('etat', $etat);
        }

        // Filtre par date
        if ($date) {
            $dateObj = new \DateTime($date);
            $startOfDay = (clone $dateObj)->setTime(0, 0, 0);
            $endOfDay = (clone $dateObj)->setTime(23, 59, 59);

            $queryBuilder->andWhere('c.dateCommande BETWEEN :start AND :end')
                ->setParameter('start', $startOfDay)
                ->setParameter('end', $endOfDay);
        }

        // Filtre par client
        if ($clientId) {
            $queryBuilder->andWhere('c.clientId = :clientId')
                ->setParameter('clientId', $clientId);
        }

        $commandes = $queryBuilder->getQuery()->getResult();

        // Récupérer tous les clients pour le filtre
        $clients = $this->userRepository->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', 'CLIENT')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();

        // États possibles
        $etats = [
            'EN_ATTENTE' => 'En attente',
            'VALIDEE' => 'Validée',
            'EN_PREPARATION' => 'En préparation',
            'TERMINEE' => 'Terminée',
            'EN_LIVRAISON' => 'En livraison',
            'LIVREE' => 'Livrée',
            'ANNULEE' => 'Annulée'
        ];

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'clients' => $clients,
            'etats' => $etats,
            'filtres' => [
                'etat' => $etat,
                'date' => $date,
                'client' => $clientId
            ]
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $commande = $this->commandeRepository->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->leftJoin('c.zone', 'zone')
            ->leftJoin('c.livreur', 'livreur')
            ->leftJoin('c.lignesCommande', 'lignes')
            ->leftJoin('lignes.burger', 'burger')
            ->leftJoin('lignes.menu', 'menu')
            ->leftJoin('lignes.complement', 'complement')
            ->addSelect('client', 'zone', 'livreur', 'lignes', 'burger', 'menu', 'complement')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande
        ]);
    }

    #[Route('/{id}/annuler', name: 'app_commande_annuler', methods: ['POST'])]
    public function annuler(int $id, Request $request): Response
    {
        $commande = $this->commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('annuler_commande_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_commande_show', ['id' => $id]);
        }

        // Vérifier que la commande peut être annulée
        if (in_array($commande->getEtat(), ['TERMINEE', 'LIVREE', 'ANNULEE'])) {
            $this->addFlash('error', 'Cette commande ne peut plus être annulée.');
            return $this->redirectToRoute('app_commande_show', ['id' => $id]);
        }

        // Annuler la commande
        $commande->setEtat('ANNULEE');
        $this->entityManager->flush();

        $this->addFlash('success', 'Commande #' . $commande->getNumeroCommande() . ' annulée avec succès.');

        return $this->redirectToRoute('app_commande_index');
    }

    #[Route('/{id}/changer-etat', name: 'app_commande_changer_etat', methods: ['POST'])]
    public function changerEtat(int $id, Request $request): Response
    {
        $commande = $this->commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('changer_etat_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_commande_show', ['id' => $id]);
        }

        $nouvelEtat = $request->request->get('etat');

        // États autorisés
        $etatsAutorises = ['EN_ATTENTE', 'VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE'];

        if (!in_array($nouvelEtat, $etatsAutorises)) {
            $this->addFlash('error', 'État invalide.');
            return $this->redirectToRoute('app_commande_show', ['id' => $id]);
        }

        // Changer l'état
        $ancienEtat = $commande->getEtat();
        $commande->setEtat($nouvelEtat);
        $this->entityManager->flush();

        $this->addFlash('success', "État de la commande #{$commande->getNumeroCommande()} changé : {$ancienEtat} → {$nouvelEtat}");

        return $this->redirectToRoute('app_commande_show', ['id' => $id]);
    }
}
