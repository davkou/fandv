<?php

namespace App\Tests\App\Service;

use App\Service\Search\SearchAdapter;
use App\Service\StorageService;
use App\Storage\InMemoryStorageAdapter;
use App\Service\Search\SearchInterface;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class StorageServiceVegetableTest extends TestCase
{
    private StorageService $storageService;
    private InMemoryStorageAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new InMemoryStorageAdapter(new SearchAdapter());
        $this->storageService = new StorageService($this->adapter);
    }

    public function testSaveAndRetrieveSingleVegetable(): void
    {
        // Arrange
        $vegetableData = [
            'type' => 'vegetable',
            'id' => 1, // valid ID
            'name' => 'Carrot',
            'grams' => 100,
        ];

        // Act
        $this->storageService->saveData($vegetableData, 'vegetables');
        $retrievedVegetable = $this->storageService->findData(1, 'vegetables'); // ID en entier

        // Assert
        $this->assertNotNull($retrievedVegetable);
        $this->assertSame('Carrot', $retrievedVegetable['name']);
        $this->assertSame(100, $retrievedVegetable['grams']);
    }

    public function testRetrieveAllVegetables(): void
    {
        // Arrange
        $this->storageService->saveData(['type' => 'vegetable', 'id' => 0, 'name' => 'Carrot', 'grams' => 100], 'vegetables');
        $this->storageService->saveData(['type' => 'vegetable', 'id' => 1, 'name' => 'Cucumber', 'grams' => 200], 'vegetables');

        // Act
        $allVegetables = $this->storageService->getAllData('vegetables', []);

        // Assert
        $this->assertCount(2, $allVegetables);
        $this->assertSame('Carrot', $allVegetables[0]['name']);
        $this->assertSame('Cucumber', $allVegetables[1]['name']);
    }

    public function testHandleNonexistentVegetable(): void
    {
        // Act
        $nonexistentVegetable = $this->storageService->findData(99, 'vegetables'); // ID en entier

        // Assert
        $this->assertNull($nonexistentVegetable);
    }

    public function testSaveVegetableWithInvalidIdType(): void
    {
        // Arrange :
        $vegetableData = [
            'type' => 'vegetable',
            'id' => '1', // invalid ID
            'name' => 'Carrot',
            'grams' => 100,
        ];

        // Assert :
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "id" key and it must be an integer.');

        // Act :
        $this->storageService->saveData($vegetableData, 'vegetables');
    }

    public function testSaveVegetableWithInvalidGrams(): void
    {
        // Arrange :
        $vegetableData = [
            'type' => 'vegetable',
            'id' => 1,
            'name' => 'Carrot',
            'grams' => -50, // Invalid grams
        ];

        // Assert :
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "grams" key and it must be greater than or equal to 0.');

        // Act :
        $this->storageService->saveData($vegetableData, 'vegetables');
    }

    public function testSaveVegetableWithMissingGrams(): void
    {
        // Arrange :
        $vegetableData = [
            'type' => 'vegetable',
            'id' => 1,
            'name' => 'Carrot',
            // No 'grams'
        ];

        // Assert :
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "grams" key and it must be greater than or equal to 0.');

        // Act :
        $this->storageService->saveData($vegetableData, 'vegetables');
    }
}
