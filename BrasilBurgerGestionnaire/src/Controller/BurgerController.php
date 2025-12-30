<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Repository\BurgerRepository;
use App\Repository\BurgerCategorieRepository;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/burgers')]
class BurgerController extends AbstractController
{
    public function __construct(
        private BurgerRepository $burgerRepository,
        private BurgerCategorieRepository $categorieRepository,
        private CloudinaryService $cloudinaryService,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {}

    #[Route('', name: 'app_burger_index')]
    public function index(Request $request): Response
    {
        $queryBuilder = $this->burgerRepository->createQueryBuilder('b')
            ->leftJoin('b.categorie', 'c')
            ->addSelect('c')
            ->orderBy('b.isArchived', 'ASC')
            ->addOrderBy('b.libelle', 'ASC');

        // Pagination
        $burgers = $this->paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('burger/index.html.twig', [
            'burgers' => $burgers
        ]);
    }


    #[Route('/nouveau', name: 'app_burger_new')]
    public function new(Request $request): Response
    {
        $categories = $this->categorieRepository->findAll();

        if ($request->isMethod('POST')) {
            // Vérifier le token CSRF
            if (!$this->isCsrfTokenValid('create_burger', $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_burger_new');
            }

            // ⬇️ AJOUTER LES VALIDATIONS
            $errors = [];

            $libelle = trim($request->request->get('libelle'));
            if (empty($libelle)) {
                $errors[] = 'Le nom du burger est obligatoire.';
            } elseif (strlen($libelle) < 3) {
                $errors[] = 'Le nom du burger doit contenir au moins 3 caractères.';
            }

            $prix = $request->request->get('prix');
            if (empty($prix) || !is_numeric($prix) || $prix < 0) {
                $errors[] = 'Le prix doit être un nombre positif valide.';
            }

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($imageFile->getMimeType(), $allowedMimes)) {
                    $errors[] = 'Format d\'image non supporté. Utilisez JPG, JPEG ou PNG.';
                }
                if ($imageFile->getSize() > 5 * 1024 * 1024) { // 5 MB
                    $errors[] = 'L\'image ne doit pas dépasser 5 MB.';
                }
            }

            // Si erreurs, afficher et rediriger
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('app_burger_new');
            }
            // ⬆️ FIN VALIDATIONS

            $burger = new Burger();
            $burger->setLibelle($libelle);
            $burger->setDescription($request->request->get('description'));
            $burger->setPrix($prix);
            $burger->setIsArchived(false);

            // Catégorie
            $categorieId = $request->request->get('categorie_id');
            if ($categorieId) {
                $categorie = $this->categorieRepository->find($categorieId);
                if ($categorie) {
                    $burger->setCategorie($categorie);
                }
            }

            // Upload de l'image
            if ($imageFile) {
                $imageUrl = $this->cloudinaryService->uploadImage($imageFile, 'brasil-burger/burgers');
                if ($imageUrl) {
                    $burger->setImageUrl($imageUrl);
                } else {
                    $this->addFlash('warning', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $this->entityManager->persist($burger);
            $this->entityManager->flush();

            $this->addFlash('success', "Burger '{$burger->getLibelle()}' créé avec succès.");

            return $this->redirectToRoute('app_burger_index');
        }

        return $this->render('burger/new.html.twig', [
            'categories' => $categories
        ]);
    }


    #[Route('/{id}/modifier', name: 'app_burger_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $burger = $this->burgerRepository->find($id);

        if (!$burger) {
            throw $this->createNotFoundException('Burger non trouvé');
        }

        $categories = $this->categorieRepository->findAll();

        if ($request->isMethod('POST')) {
            // Vérifier le token CSRF
            if (!$this->isCsrfTokenValid('edit_burger_' . $id, $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_burger_edit', ['id' => $id]);
            }

            // ⬇️ AJOUTER LES VALIDATIONS
            $errors = [];

            $libelle = trim($request->request->get('libelle'));
            if (empty($libelle)) {
                $errors[] = 'Le nom du burger est obligatoire.';
            } elseif (strlen($libelle) < 3) {
                $errors[] = 'Le nom du burger doit contenir au moins 3 caractères.';
            }

            $prix = $request->request->get('prix');
            if (empty($prix) || !is_numeric($prix) || $prix < 0) {
                $errors[] = 'Le prix doit être un nombre positif valide.';
            }

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($imageFile->getMimeType(), $allowedMimes)) {
                    $errors[] = 'Format d\'image non supporté. Utilisez JPG, JPEG ou PNG.';
                }
                if ($imageFile->getSize() > 5 * 1024 * 1024) { // 5 MB
                    $errors[] = 'L\'image ne doit pas dépasser 5 MB.';
                }
            }

            // Si erreurs, afficher et rediriger
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('app_burger_edit', ['id' => $id]);
            }
            // ⬆️ FIN VALIDATIONS

            $burger->setLibelle($libelle);
            $burger->setDescription($request->request->get('description'));
            $burger->setPrix($prix);

            // Catégorie
            $categorieId = $request->request->get('categorie_id');
            if ($categorieId) {
                $categorie = $this->categorieRepository->find($categorieId);
                $burger->setCategorie($categorie);
            } else {
                $burger->setCategorie(null);
            }

            // Upload de l'image
            if ($imageFile) {
                // Supprimer l'ancienne image si c'est une URL Cloudinary
                if ($burger->getImageUrl() && $this->cloudinaryService->isCloudinaryUrl($burger->getImageUrl())) {
                    $this->cloudinaryService->deleteImage($burger->getImageUrl());
                }

                // Upload la nouvelle image
                $imageUrl = $this->cloudinaryService->uploadImage($imageFile, 'brasil-burger/burgers');
                if ($imageUrl) {
                    $burger->setImageUrl($imageUrl);
                } else {
                    $this->addFlash('warning', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', "Burger '{$burger->getLibelle()}' modifié avec succès.");

            return $this->redirectToRoute('app_burger_index');
        }

        return $this->render('burger/edit.html.twig', [
            'burger' => $burger,
            'categories' => $categories
        ]);
    }


    #[Route('/{id}/archiver', name: 'app_burger_archive', methods: ['POST'])]
    public function archive(int $id, Request $request): Response
    {
        $burger = $this->burgerRepository->find($id);

        if (!$burger) {
            throw $this->createNotFoundException('Burger non trouvé');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('archive_burger_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_burger_index');
        }

        $burger->setIsArchived(!$burger->isArchived());
        $this->entityManager->flush();

        $status = $burger->isArchived() ? 'archivé' : 'désarchivé';
        $this->addFlash('success', "Burger '{$burger->getLibelle()}' {$status}.");

        return $this->redirectToRoute('app_burger_index');
    }
}
