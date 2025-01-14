<?php

namespace App\Storage;

interface StorageAdapterInterface
{
    public function save(string $id, array $data, string $repository): void;
    public function find(string $id, string $repository): ?array;
    public function findAll(string $repository): array;
}
