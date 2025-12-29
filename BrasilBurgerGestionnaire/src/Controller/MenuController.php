<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\BurgerRepository;
use App\Repository\ComplementRepository;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/menus')]
class MenuController extends AbstractController
{
    public function __construct(
        private MenuRepository $menuRepository,
        private BurgerRepository $burgerRepository,
        private ComplementRepository $complementRepository,
        private CloudinaryService $cloudinaryService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_menu_index')]
    public function index(): Response
    {
        $menus = $this->menuRepository->createQueryBuilder('m')
            ->leftJoin('m.burger', 'b')
            ->leftJoin('m.boisson', 'bo')
            ->leftJoin('m.frite', 'f')
            ->addSelect('b', 'bo', 'f')
            ->orderBy('m.isArchived', 'ASC')
            ->addOrderBy('m.libelle', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('menu/index.html.twig', [
            'menus' => $menus
        ]);
    }

    #[Route('/nouveau', name: 'app_menu_new')]
    public function new(Request $request): Response
    {
        $burgers = $this->burgerRepository->findNonArchived();
        $boissons = $this->complementRepository->findByType('BOISSON');
        $frites = $this->complementRepository->findByType('FRITE');

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('create_menu', $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_menu_new');
            }

            $menu = new Menu();
            $menu->setLibelle($request->request->get('libelle'));
            $menu->setIsArchived(false);

            // Associer le burger
            $burgerId = $request->request->get('burger_id');
            $burger = $this->burgerRepository->find($burgerId);
            if ($burger) {
                $menu->setBurger($burger);
            }

            // Associer la boisson
            $boissonId = $request->request->get('boisson_id');
            $boisson = $this->complementRepository->find($boissonId);
            if ($boisson) {
                $menu->setBoisson($boisson);
            }

            // Associer les frites
            $friteId = $request->request->get('frite_id');
            $frite = $this->complementRepository->find($friteId);
            if ($frite) {
                $menu->setFrite($frite);
            }

            // Upload de l'image
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $imageUrl = $this->cloudinaryService->uploadImage($imageFile, 'brasil-burger/menus');
                if ($imageUrl) {
                    $menu->setImageUrl($imageUrl);
                }
            }

            $this->entityManager->persist($menu);
            $this->entityManager->flush();

            $this->addFlash('success', "Menu '{$menu->getLibelle()}' créé avec succès.");

            return $this->redirectToRoute('app_menu_index');
        }

        return $this->render('menu/new.html.twig', [
            'burgers' => $burgers,
            'boissons' => $boissons,
            'frites' => $frites
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_menu_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $menu = $this->menuRepository->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Menu non trouvé');
        }

        $burgers = $this->burgerRepository->findNonArchived();
        $boissons = $this->complementRepository->findByType('BOISSON');
        $frites = $this->complementRepository->findByType('FRITE');

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('edit_menu_' . $id, $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_menu_edit', ['id' => $id]);
            }

            $menu->setLibelle($request->request->get('libelle'));

            // Mettre à jour les composants
            $burger = $this->burgerRepository->find($request->request->get('burger_id'));
            $menu->setBurger($burger);

            $boisson = $this->complementRepository->find($request->request->get('boisson_id'));
            $menu->setBoisson($boisson);

            $frite = $this->complementRepository->find($request->request->get('frite_id'));
            $menu->setFrite($frite);

            // Upload image
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                if ($menu->getImageUrl() && $this->cloudinaryService->isCloudinaryUrl($menu->getImageUrl())) {
                    $this->cloudinaryService->deleteImage($menu->getImageUrl());
                }

                $imageUrl = $this->cloudinaryService->uploadImage($imageFile, 'brasil-burger/menus');
                if ($imageUrl) {
                    $menu->setImageUrl($imageUrl);
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', "Menu '{$menu->getLibelle()}' modifié avec succès.");

            return $this->redirectToRoute('app_menu_index');
        }

        return $this->render('menu/edit.html.twig', [
            'menu' => $menu,
            'burgers' => $burgers,
            'boissons' => $boissons,
            'frites' => $frites
        ]);
    }

    #[Route('/{id}/archiver', name: 'app_menu_archive', methods: ['POST'])]
    public function archive(int $id, Request $request): Response
    {
        $menu = $this->menuRepository->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Menu non trouvé');
        }

        if (!$this->isCsrfTokenValid('archive_menu_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_menu_index');
        }

        $menu->setIsArchived(!$menu->isArchived());
        $this->entityManager->flush();

        $status = $menu->isArchived() ? 'archivé' : 'désarchivé';
        return $this->redirectToRoute('app_menu_index');
    }
}
