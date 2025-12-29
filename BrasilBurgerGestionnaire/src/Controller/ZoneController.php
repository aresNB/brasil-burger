<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Entity\Quartier;
use App\Repository\ZoneRepository;
use App\Repository\QuartierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/zones')]
class ZoneController extends AbstractController
{
    public function __construct(
        private ZoneRepository $zoneRepository,
        private QuartierRepository $quartierRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_zone_index')]
    public function index(): Response
    {
        $zones = $this->zoneRepository->createQueryBuilder('z')
            ->leftJoin('z.quartiers', 'q')
            ->addSelect('q')
            ->orderBy('z.nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('zone/index.html.twig', [
            'zones' => $zones
        ]);
    }

    #[Route('/nouvelle', name: 'app_zone_new')]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Vérifier le token CSRF
            if (!$this->isCsrfTokenValid('create_zone', $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_zone_new');
            }

            $zone = new Zone();
            $zone->setNom($request->request->get('nom'));
            $zone->setPrixLivraison($request->request->get('prix_livraison'));
            $zone->setActif($request->request->get('actif') === '1');

            $this->entityManager->persist($zone);
            $this->entityManager->flush();

            $this->addFlash('success', "Zone '{$zone->getNom()}' créée avec succès.");

            return $this->redirectToRoute('app_zone_index');
        }

        return $this->render('zone/new.html.twig');
    }

    #[Route('/{id}', name: 'app_zone_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $zone = $this->zoneRepository->createQueryBuilder('z')
            ->leftJoin('z.quartiers', 'q')
            ->addSelect('q')
            ->where('z.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$zone) {
            throw $this->createNotFoundException('Zone non trouvée');
        }

        return $this->render('zone/show.html.twig', [
            'zone' => $zone
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_zone_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $zone = $this->zoneRepository->find($id);

        if (!$zone) {
            throw $this->createNotFoundException('Zone non trouvée');
        }

        if ($request->isMethod('POST')) {
            // Vérifier le token CSRF
            if (!$this->isCsrfTokenValid('edit_zone_' . $id, $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_zone_edit', ['id' => $id]);
            }

            $zone->setNom($request->request->get('nom'));
            $zone->setPrixLivraison($request->request->get('prix_livraison'));
            $zone->setActif($request->request->get('actif') === '1');

            $this->entityManager->flush();

            $this->addFlash('success', "Zone '{$zone->getNom()}' modifiée avec succès.");

            return $this->redirectToRoute('app_zone_show', ['id' => $id]);
        }

        return $this->render('zone/edit.html.twig', [
            'zone' => $zone
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_zone_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $zone = $this->zoneRepository->find($id);

        if (!$zone) {
            throw $this->createNotFoundException('Zone non trouvée');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('delete_zone_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_zone_show', ['id' => $id]);
        }

        // Vérifier qu'il n'y a pas de commandes liées
        $commandesCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from('App\Entity\Commande', 'c')
            ->where('c.zoneId = :zoneId')
            ->setParameter('zoneId', $id)
            ->getQuery()
            ->getSingleScalarResult();

        if ($commandesCount > 0) {
            $this->addFlash('error', "Impossible de supprimer cette zone car {$commandesCount} commande(s) y sont liées.");
            return $this->redirectToRoute('app_zone_show', ['id' => $id]);
        }

        $nomZone = $zone->getNom();
        $this->entityManager->remove($zone);
        $this->entityManager->flush();

        $this->addFlash('success', "Zone '{$nomZone}' supprimée avec succès.");

        return $this->redirectToRoute('app_zone_index');
    }

    #[Route('/{id}/quartiers/ajouter', name: 'app_zone_quartier_add', methods: ['POST'])]
    public function addQuartier(int $id, Request $request): Response
    {
        $zone = $this->zoneRepository->find($id);

        if (!$zone) {
            throw $this->createNotFoundException('Zone non trouvée');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('add_quartier_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_zone_show', ['id' => $id]);
        }

        $quartier = new Quartier();
        $quartier->setNom($request->request->get('nom'));
        $quartier->setCodePostal($request->request->get('code_postal'));
        $quartier->setZone($zone);

        $this->entityManager->persist($quartier);
        $this->entityManager->flush();

        $this->addFlash('success', "Quartier '{$quartier->getNom()}' ajouté à la zone '{$zone->getNom()}'.");

        return $this->redirectToRoute('app_zone_show', ['id' => $id]);
    }

    #[Route('/{zoneId}/quartiers/{quartierId}/supprimer', name: 'app_zone_quartier_delete', methods: ['POST'])]
    public function deleteQuartier(int $zoneId, int $quartierId, Request $request): Response
    {
        $quartier = $this->quartierRepository->find($quartierId);

        if (!$quartier || $quartier->getZoneId() !== $zoneId) {
            throw $this->createNotFoundException('Quartier non trouvé');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('delete_quartier_' . $quartierId, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_zone_show', ['id' => $zoneId]);
        }

        $nomQuartier = $quartier->getNom();
        $this->entityManager->remove($quartier);
        $this->entityManager->flush();

        $this->addFlash('success', "Quartier '{$nomQuartier}' supprimé.");

        return $this->redirectToRoute('app_zone_show', ['id' => $zoneId]);
    }
}
