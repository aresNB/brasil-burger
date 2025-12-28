<?php

namespace App\Controller;

use App\Service\StatistiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private StatistiqueService $statistiqueService
    ) {}

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        // Vérifier que l'utilisateur est bien un gestionnaire
        $user = $this->getUser();
        if (!$user || $user->getRole() !== 'GESTIONNAIRE') {
            $this->addFlash('error', 'Accès réservé aux gestionnaires.');
            return $this->redirectToRoute('app_logout');
        }

        // Récupérer les statistiques du jour
        $statistiques = $this->statistiqueService->getStatistiquesJour();

        return $this->render('dashboard/index.html.twig', [
            'statistiques' => $statistiques,
        ]);
    }
}
