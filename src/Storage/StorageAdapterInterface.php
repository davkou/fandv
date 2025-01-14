<?php

namespace App\Storage;

interface StorageAdapterInterface
{
    public function add(int $id, array $data, string $repository): void;
    public function get(int $id, string $repository): ?array;
    public function list(string $repository, array $filters): array;
}
