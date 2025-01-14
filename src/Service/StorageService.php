<?php

namespace App\Service;

use App\Storage\StorageAdapterInterface;

class StorageService
{
    public function __construct(
        private StorageAdapterInterface $adapter,
        private ?string $request = '',
    ) {
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function saveData(array $data, string $repository): void
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException('Data must contain an "id" key.');
        }

        $this->adapter->save($data['id'], $data, $repository);
    }

    public function findData(string $id, string $repository): ?array
    {
        return $this->adapter->find($id, $repository);
    }

    public function getAllData(string $repository): array
    {
        return $this->adapter->findAll($repository);
    }
}
