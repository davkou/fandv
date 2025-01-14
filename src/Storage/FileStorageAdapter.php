<?php

namespace App\Storage;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileStorageAdapter implements StorageAdapterInterface
{
    private string $basePath;

    public function __construct(string $basePath = '/data/storage')
    {
        $this->basePath = $basePath;
        $this->ensureBasePathExists();
    }

    // S'assurer que le répertoire de base existe
    private function ensureBasePathExists(): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->basePath)) {
            $filesystem->mkdir($this->basePath);
        }
    }

    // Sauvegarder des données dans un fichier spécifique à un repository (fruits ou légumes)
    public function save(string $id, array $data, string $repository): void
    {
        // Déterminer le chemin du fichier pour ce repository
        $filePath = $this->basePath . '/' . $repository . '.json';

        // Charger les données existantes si le fichier existe
        $existingData = [];
        if (file_exists($filePath)) {
            $existingData = json_decode(file_get_contents($filePath), true);
        }

        // Si l'id existe déjà, on met à jour, sinon on ajoute une nouvelle entrée dans le tableau
        $updated = false;
        foreach ($existingData as &$item) {
            if ($item['id'] === $id) {
                $item = $data; // Mettre à jour les données pour cet ID
                $updated = true;
                break;
            }
        }

        // Si l'élément n'existe pas, on l'ajoute à la liste
        if (!$updated) {
            $existingData[] = $data;
        }

        // Sauvegarder toutes les données mises à jour dans le fichier
        file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));
    }

    // Récupérer une donnée par son ID depuis un repository (fruits ou légumes)
    public function find(string $id, string $repository): ?array
    {
        // Déterminer le chemin du fichier pour ce repository
        $filePath = $this->basePath . '/' . $repository . '.json';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            return null;
        }

        // Charger toutes les données et rechercher l'élément correspondant à l'ID
        $data = json_decode(file_get_contents($filePath), true);
        foreach ($data as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    // Récupérer toutes les données d'un repository
    public function findAll(string $repository): array
    {
        // Déterminer le chemin du fichier pour ce repository
        $filePath = $this->basePath . '/' . $repository . '.json';

        // Si le fichier n'existe pas, retourner un tableau vide
        if (!file_exists($filePath)) {
            return [];
        }

        // Lire et retourner toutes les données du fichier
        $data = file_get_contents($filePath);
        return json_decode($data, true) ?? [];
    }

    // Vérifier et créer le répertoire du repository s'il n'existe pas
    private function ensureRepositoryExists(string $repositoryPath): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($repositoryPath)) {
            $filesystem->mkdir($repositoryPath);
        }
    }
}
