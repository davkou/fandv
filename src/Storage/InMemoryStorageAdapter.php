<?php

namespace App\Storage;

use App\Service\Search\SearchInterface;

class InMemoryStorageAdapter implements StorageAdapterInterface
{
    private array $storage = [];
    private SearchInterface $search;

    public function __construct(SearchInterface $search)
    {
        $this->search = $search;
    }

    // Add or update an entry in the storage
    public function add(int $id, array $data, string $repository): void
    {
        // Validate the ID: it must be an integer for fruits
        if (!is_int($id)) {
            throw new \InvalidArgumentException("ID must be an integer.");
        }

        // Add or update the data in the repository
        $this->storage[$repository][$id] = $data;
    }

    // Retrieve data by its ID from a repository
    public function get(int $id, string $repository): ?array
    {
        // Validate the ID: it must be an integer for fruits
        if (!is_int($id)) {
            throw new \InvalidArgumentException("ID must be an integer.");
        }

        // Return the repository data if it exists
        return $this->storage[$repository][$id] ?? null;
    }

    // Retrieve all data from a repository with optional filtering
    public function list(string $repository, array $filters = []): array
    {
        // Return all data if no filters are applied
        $items = $this->storage[$repository] ?? [];

        // If filters are present, apply them
        if (!empty($filters)) {
            return $this->search->search($filters, $items);
        }

        return $items;
    }

    // Search for a specific item in a repository (optional)
    public function search(string $repository, array $filters): array
    {
        // Check if the repository exists
        if (!isset($this->storage[$repository])) {
            return [];
        }

        // Apply the search using the SearchInterface service
        return $this->search->search($filters, $this->storage[$repository]);
    }
}
