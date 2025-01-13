<?php

namespace App\Storage;

interface StorageAdapterInterface
{
	public function save(string $id, array $data): void;
	public function find(string $id): ?array;
	public function findAll(): array;
}