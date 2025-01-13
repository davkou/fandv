<?php

namespace App\Service;

use App\Storage\StorageAdapterInterface;

class StorageService
{
	private StorageAdapterInterface $adapter;

	public function __construct(
		private string $request,
		StorageAdapterInterface $adapter
	) {
		$this->adapter = $adapter;
	}

	public function getRequest(): string
	{
		return $this->request;
	}

	public function saveData(array $data): void
	{
		if (!isset($data['id'])) {
			throw new \InvalidArgumentException('Data must contain an "id" key.');
		}

		$this->adapter->save($data['id'], $data);
	}

	public function findData(string $id): ?array
	{
		return $this->adapter->find($id);
	}

	public function getAllData(): array
	{
		return $this->adapter->findAll();
	}
}