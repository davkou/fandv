<?php

/// tests/Service/StorageServiceVegetableTest.php
namespace App\Tests\Service;

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
		$storageService = new StorageService($request, $adapter);
		$vegetableData = json_decode($request, true);

		// Act
		$storageService->saveData($vegetableData);
		$retrievedVegetable = $storageService->findData('1');

		// Assert
		$this->assertNotNull($retrievedVegetable);
		$this->assertSame('Carrot', $retrievedVegetable['name']);
		$this->assertSame(100, $retrievedVegetable['grams']);
	}

	public function testRetrieveAllVegetables(): void
	{
		// Arrange
		$adapter = new InMemoryStorageAdapter();
		$storageService = new StorageService('', $adapter);

		$storageService->saveData(['type' => 'vegetable', 'id' => '1', 'name' => 'Carrot', 'grams' => 100]);
		$storageService->saveData(['type' => 'vegetable', 'id' => '2', 'name' => 'Cucumber', 'grams' => 200]);

		// Act
		$allVegetables = $storageService->getAllData();

		// Assert
		$this->assertCount(2, $allVegetables);
		$this->assertSame('Carrot', $allVegetables[0]['name']);
		$this->assertSame('Cucumber', $allVegetables[1]['name']);
	}

	public function testHandleNonexistentVegetable(): void
	{
		// Arrange
		$adapter = new InMemoryStorageAdapter();
		$storageService = new StorageService('', $adapter);

		// Act
		$nonexistentVegetable = $storageService->findData('99');

		// Assert
		$this->assertNull($nonexistentVegetable);
	}
}