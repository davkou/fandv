<?php

namespace App\Tests\App\Service;

use App\Service\StorageService;
use App\Storage\InMemoryStorageAdapter;
use PHPUnit\Framework\TestCase;

class StorageServiceFruitTest extends TestCase
{
    public function testSaveAndRetrieveSingleFruit(): void
    {
        // Arrange
        $request = '{"type": "fruit", "id": "1", "name": "Apple", "grams": 150}';
        $adapter = new InMemoryStorageAdapter();
        $storageService = new StorageService($adapter, $request);
        $fruitData = json_decode($request, true);

        // Act
        $storageService->saveData($fruitData, 'fruit');
        $retrievedFruit = $storageService->findData('1', 'fruit');

        // Assert
        $this->assertNotNull($retrievedFruit);
        $this->assertSame('Apple', $retrievedFruit['name'], 'fruits');
        $this->assertSame(150, $retrievedFruit['grams'], 'fruits');
    }

    public function testRetrieveAllFruits(): void
    {
        // Arrange
        $adapter = new InMemoryStorageAdapter();
        $storageService = new StorageService($adapter);

        $storageService->saveData(['type' => 'fruit', 'id' => '1', 'name' => 'Apple', 'grams' => 150], 'fruits');
        $storageService->saveData(['type' => 'fruit', 'id' => '2', 'name' => 'Banana', 'grams' => 120], 'fruits');

        // Act
        $allFruits = $storageService->getAllData('fruits');

        // Assert
        $this->assertCount(2, $allFruits);
        $this->assertSame('Apple', $allFruits[0]['name'], 'fruits');
        $this->assertSame('Banana', $allFruits[1]['name'], 'fruits');
    }

    public function testHandleNonexistentFruit(): void
    {
        // Arrange
        $adapter = new InMemoryStorageAdapter();
        $storageService = new StorageService($adapter);

        // Act
        $nonexistentFruit = $storageService->findData('99', 'fruit');

        // Assert
        $this->assertNull($nonexistentFruit);
    }
}
