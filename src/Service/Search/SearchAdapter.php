<?php

namespace App\Service\Search;

class SearchAdapter implements SearchInterface
{
    public function search(array $criteria, array $data): array
    {
        return array_filter($data, function ($item) use ($criteria) {
            foreach ($criteria as $key => $value) {
                if (is_array($value)) {
                    // Handle value ranges (min/max)
                    if (isset($value['min']) && $item[$key] < $value['min']) {
                        return false;
                    }
                    if (isset($value['max']) && $item[$key] > $value['max']) {
                        return false;
                    }
                } else {
                    // Partial search for string values
                    if (is_string($item[$key])) {
                        // Partial match (wildcard matching)
                        if (isset($value['wildcard'])) {
                            if (stripos($item[$key], $value['wildcard']) === false) {
                                return false;
                            }
                        } else {
                            // Exact match
                            if (stripos($item[$key], $value) === false) {
                                return false;
                            }
                        }
                    } else {
                        // Exact match for other types (e.g., number, bool, etc.)
                        if ($item[$key] != $value) {
                            return false;
                        }
                    }
                }
            }
            return true;
        });
    }
}
