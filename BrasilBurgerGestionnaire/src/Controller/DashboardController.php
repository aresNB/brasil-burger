<?php

namespace App\Controller;

use App\Entity\User;
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
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier que l'utilisateur est bien un gestionnaire
        if (!$user || !in_array('ROLE_GESTIONNAIRE', $user->getRoles())) {
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
