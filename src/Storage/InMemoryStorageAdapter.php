<?php

namespace App\Storage;

class InMemoryStorageAdapter implements StorageAdapterInterface
{
    private array $storage = [];

    public function save(string $id, array $data, string $repository): void
    {
        $this->storage[$repository][$id] = $data;
    }

    public function find(string $id, string $repository): ?array
    {
        return $this->storage[$repository][$id] ?? null;
    }

    public function findAll(string $repository): array
    {
        return array_values($this->storage[$repository]);
    }
}
