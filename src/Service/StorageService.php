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
        // Check if "id" is present and is an integer
        if (!isset($data['id']) || !is_int($data['id'])) {
            throw new \InvalidArgumentException('Data must contain a valid "id" key and it must be an integer.');
        }

        // Check if "grams" is present and is a positive number
        if (!isset($data['grams']) || $data['grams'] < 0) {
            throw new \InvalidArgumentException('Data must contain a valid "grams" key and it must be greater than or equal to 0.');
        }

        // Check if "name" is present and is a string
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new \InvalidArgumentException('Data must contain a valid "name" key and it must be a string and not blank!.');
        }

        // If validations pass, add the data to the adapter
        $this->adapter->add($data['id'], $data, $repository);
    }

    public function findData(string $id, string $repository): ?array
    {
        return $this->adapter->get($id, $repository);
    }

    public function getAllData(string $repository, array $filters): array
    {
        return $this->adapter->list($repository, $filters);
    }
}
