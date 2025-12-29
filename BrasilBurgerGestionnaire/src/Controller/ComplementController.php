<?php

namespace App\Controller;

use App\Entity\Complement;
use App\Repository\ComplementRepository;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/complements')]
class ComplementController extends AbstractController
{
    public function __construct(
        private ComplementRepository $complementRepository,
        private CloudinaryService $cloudinaryService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_complement_index')]
    public function index(): Response
    {
        $complements = $this->complementRepository->createQueryBuilder('c')
            ->orderBy('c.type', 'ASC')
            ->addOrderBy('c.isArchived', 'ASC')
            ->addOrderBy('c.libelle', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('complement/index.html.twig', [
            'complements' => $complements
        ]);
    }

    #[Route('/nouveau', name: 'app_complement_new')]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('create_complement', $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_complement_new');
            }

            $complement = new Complement();
            $complement->setLibelle($request->request->get('libelle'));
            $complement->setPrix($request->request->get('prix'));
            $complement->setType($request->request->get('type'));
            $complement->setIsArchived(false);

            // Upload de l'image
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $imageUrl = $this->cloudinaryService->uploadImage($imageFile, 'brasil-burger/complements');
                if ($imageUrl) {
                    $complement->setImageUrl($imageUrl);
                } else {
                    $this->addFlash('warning', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $this->entityManager->persist($complement);
            $this->entityManager->flush();

            $this->addFlash('success', "Complément '{$complement->getLibelle()}' créé avec succès.");

            return $this->redirectToRoute('app_complement_index');
        }

        return $this->render('complement/new.html.twig');
    }

    #[Route('/{id}/modifier', name: 'app_complement_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $complement = $this->complementRepository->find($id);

        if (!$complement) {
            throw $this->createNotFoundException('Complément non trouvé');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('edit_complement_' . $id, $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_complement_edit', ['id' => $id]);
            }

            $complement->setLibelle($request->request->get('libelle'));
            $complement->setPrix($request->request->get('prix'));
            $complement->setType($request->request->get('type'));

            // Upload de l'image
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                if ($complement->getImageUrl() && $this->cloudinaryService->isCloudinaryUrl($complement->getImageUrl())) {
                    $this->cloudinaryService->deleteImage($complement->getImageUrl());
                }

                $imageUrl = $this->cloudinaryService->uploadImage($imageFile, 'brasil-burger/complements');
                if ($imageUrl) {
                    $complement->setImageUrl($imageUrl);
                } else {
                    $this->addFlash('warning', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', "Complément '{$complement->getLibelle()}' modifié avec succès.");

            return $this->redirectToRoute('app_complement_index');
        }

        return $this->render('complement/edit.html.twig', [
            'complement' => $complement
        ]);
    }

    #[Route('/{id}/archiver', name: 'app_complement_archive', methods: ['POST'])]
    public function archive(int $id, Request $request): Response
    {
        $complement = $this->complementRepository->find($id);

        if (!$complement) {
            throw $this->createNotFoundException('Complément non trouvé');
        }

        if (!$this->isCsrfTokenValid('archive_complement_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_complement_index');
        }

        $complement->setIsArchived(!$complement->isArchived());
        $this->entityManager->flush();

        $status = $complement->isArchived() ? 'archivé' : 'désarchivé';
        $this->addFlash('success', "Complément '{$complement->getLibelle()}' {$status}.");

        return $this->redirectToRoute('app_complement_index');
    }
}
