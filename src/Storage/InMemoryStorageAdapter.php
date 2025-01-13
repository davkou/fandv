<?php

namespace App\Storage;

class InMemoryStorageAdapter implements StorageAdapterInterface
{
	private array $storage = [];

	public function save(string $id, array $data): void
	{
		$this->storage[$id] = $data;
	}

	public function find(string $id): ?array
	{
		return $this->storage[$id] ?? null;
	}

	public function findAll(): array
	{
		return array_values($this->storage);
	}
}