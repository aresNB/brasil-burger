<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key' => $_ENV['CLOUDINARY_API_KEY'],
                'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
            ]
        ]);
    }

    /**
     * Upload une image vers Cloudinary
     */
    public function uploadImage(UploadedFile $file, string $folder = 'brasil-burger'): ?string
    {
        try {
            $uploadApi = new UploadApi();

            $result = $uploadApi->upload($file->getPathname(), [
                'folder' => $folder,
                'resource_type' => 'image',
                'transformation' => [
                    'width' => 800,
                    'height' => 600,
                    'crop' => 'limit',
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
            ]);

            return $result['secure_url'] ?? null;
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
            return null;
        }
    }

    /**
     * Supprimer une image de Cloudinary
     */
    public function deleteImage(string $imageUrl): bool
    {
        try {
            // Extraire le public_id de l'URL
            $publicId = $this->getPublicIdFromUrl($imageUrl);

            if (!$publicId) {
                return false;
            }

            $uploadApi = new UploadApi();
            $uploadApi->destroy($publicId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extraire le public_id d'une URL Cloudinary
     */
    private function getPublicIdFromUrl(string $url): ?string
    {
        // Exemple d'URL: https://res.cloudinary.com/cloud_name/image/upload/v123456/brasil-burger/burger.jpg
        // On veut extraire: brasil-burger/burger

        if (!str_contains($url, 'cloudinary.com')) {
            return null;
        }

        $parts = explode('/upload/', $url);
        if (count($parts) !== 2) {
            return null;
        }

        $pathParts = explode('/', $parts[1]);
        // Enlever la version (v123456)
        array_shift($pathParts);

        // Enlever l'extension du fichier
        $lastPart = array_pop($pathParts);
        $lastPart = pathinfo($lastPart, PATHINFO_FILENAME);
        $pathParts[] = $lastPart;

        return implode('/', $pathParts);
    }

    /**
     * Vérifier si l'image est une URL Cloudinary
     */
    public function isCloudinaryUrl(string $url): bool
    {
        return str_contains($url, 'cloudinary.com');
    }
}
