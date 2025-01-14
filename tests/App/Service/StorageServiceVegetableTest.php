<?php

namespace App\Tests\App\Service;

use App\Service\StorageService;
use App\Storage\InMemoryStorageAdapter;
use PHPUnit\Framework\TestCase;

class StorageServiceVegetableTest extends TestCase
{
    public function testSaveAndRetrieveSingleVegetable(): void
    {
        // Arrange
        $request = '{"type": "vegetable", "id": "1", "name": "Carrot", "grams": 100}';
        $adapter = new InMemoryStorageAdapter();
        $storageService = new StorageService($adapter, $request);
        $vegetableData = json_decode($request, true);

        // Act
        $storageService->saveData($vegetableData, 'vegetables');
        $retrievedVegetable = $storageService->findData('1', 'vegetables');

        // Assert
        $this->assertNotNull($retrievedVegetable);
        $this->assertSame('Carrot', $retrievedVegetable['name'], 'vegetables');
        $this->assertSame(100, $retrievedVegetable['grams'], 'vegetables');
    }

    public function testRetrieveAllVegetables(): void
    {
        // Arrange
        $adapter = new InMemoryStorageAdapter();
        $storageService = new StorageService($adapter);

        $storageService->saveData(['type' => 'vegetable', 'id' => '1', 'name' => 'Carrot', 'grams' => 100], 'vegetables');
        $storageService->saveData(['type' => 'vegetable', 'id' => '2', 'name' => 'Cucumber', 'grams' => 200], 'vegetables');

        // Act
        $allVegetables = $storageService->getAllData('vegetables');

        // Assert
        $this->assertCount(2, $allVegetables);
        $this->assertSame('Carrot', $allVegetables[0]['name']);
        $this->assertSame('Cucumber', $allVegetables[1]['name']);
    }

    public function testHandleNonexistentVegetable(): void
    {
        // Arrange
        $adapter = new InMemoryStorageAdapter();
        $storageService = new StorageService($adapter);

        // Act
        $nonexistentVegetable = $storageService->findData('99', 'vegetables');

        // Assert
        $this->assertNull($nonexistentVegetable);
    }
}
