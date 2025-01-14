<?php

namespace App\Storage;

use App\Service\Search\SearchInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileStorageAdapter implements StorageAdapterInterface
{
    private string $basePath;

    public function __construct(
        private SearchInterface $search,
        string $basePath = '/data/storage'
    ) {
        $this->basePath = $basePath;
        $this->ensureBasePathExists();
    }

    // Ensure the base directory exists
    private function ensureBasePathExists(): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->basePath)) {
            $filesystem->mkdir($this->basePath);
        }
    }

    // Save data in a specific file for a repository (fruits or vegetables)
    public function add(int $id, array $data, string $repository): void
    {
        // Determine the file path for this repository
        $filePath = $this->basePath . '/' . $repository . '.json';

        // Load existing data if the file exists
        $existingData = [];
        if (file_exists($filePath)) {
            $existingData = json_decode(file_get_contents($filePath), true);
        }

        // If the ID exists, update it; otherwise, add a new entry to the array
        $updated = false;
        foreach ($existingData as &$item) {
            if ($item['id'] === $id) {
                $item = $data; // Update the data for this ID
                $updated = true;
                break;
            }
        }

        // If the item doesn't exist, add it to the list
        if (!$updated) {
            $existingData[] = $data;
        }

        // Save all the updated data back into the file
        file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));
    }

    // Retrieve data by its ID from a repository (fruits or vegetables)
    public function get(int $id, string $repository): ?array
    {
        // Determine the file path for this repository
        $filePath = $this->basePath . '/' . $repository . '.json';

        // Check if the file exists
        if (!file_exists($filePath)) {
            return null;
        }

        // Load all the data and search for the item with the given ID
        $data = json_decode(file_get_contents($filePath), true);
        foreach ($data as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    // Retrieve all data from a repository
    public function list(string $repository, array $filters = []): array
    {
        // Determine the file path for this repository
        $filePath = $this->basePath . '/' . $repository . '.json';

        // If the file doesn't exist, return an empty array
        if (!file_exists($filePath)) {
            return [];
        }

        // Read data from the JSON file
        $data = file_get_contents($filePath);
        $items = json_decode($data, true) ?? [];

        // If no filters are set, return all the data
        if (empty($filters)) {
            return $items;
        }

        // Apply the filters
        return $this->search->search($filters, $items);
    }
}
