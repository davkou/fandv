<?php

namespace App\Service\Search;

interface SearchInterface
{
    public function search(array $criteria, array $data): array;
}
