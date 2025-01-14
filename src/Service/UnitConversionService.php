<?php

namespace App\Service;

class UnitConversionService
{
    /**
     * Converts a weight based on the requested unit.
     *
     * @param int|float $weight Initial weight in grams.
     * @param string $unit Target unit ("grams" or "kilograms").
     * @return int|float Converted weight.
     */
    public function convertWeight(int|float $weight, string $unit): int|float
    {
        return $unit === 'kilograms' ? $weight / 1000 : $weight;
    }

    /**
     * Applies unit conversion to an array of items.
     *
     * @param array $items List of items with 'grams' fields.
     * @param string $unit Target unit.
     * @return array Array with converted weights.
     */
    public function applyUnitConversion(array $items, string $unit): array
    {
        // Check if it's a single item (non-associative structure)
        if (isset($items['grams'])) {
            $items['grams'] = $this->convertWeight($items['grams'], $unit);
            return $items;
        }

        // If multiple items are provided
        foreach ($items as &$item) {
            if (isset($item['grams'])) {
                $item['grams'] = $this->convertWeight($item['grams'], $unit);
            }
        }

        return $items;
    }
}
