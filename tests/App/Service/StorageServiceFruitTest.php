<?php

namespace App\Tests\Service;

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
		$storageService = new StorageService($request, $adapter);
		$fruitData = json_decode($request, true);

		// Act
		$storageService->saveData($fruitData);
		$retrievedFruit = $storageService->findData('1');

		// Assert
		$this->assertNotNull($retrievedFruit);
		$this->assertSame('Apple', $retrievedFruit['name']);
		$this->assertSame(150, $retrievedFruit['grams']);
	}

	public function testRetrieveAllFruits(): void
	{
		// Arrange
		$adapter = new InMemoryStorageAdapter();
		$storageService = new StorageService('', $adapter);

		$storageService->saveData(['type' => 'fruit', 'id' => '1', 'name' => 'Apple', 'grams' => 150]);
		$storageService->saveData(['type' => 'fruit', 'id' => '2', 'name' => 'Banana', 'grams' => 120]);

		// Act
		$allFruits = $storageService->getAllData();

		// Assert
		$this->assertCount(2, $allFruits);
		$this->assertSame('Apple', $allFruits[0]['name']);
		$this->assertSame('Banana', $allFruits[1]['name']);
	}

	public function testHandleNonexistentFruit(): void
	{
		// Arrange
		$adapter = new InMemoryStorageAdapter();
		$storageService = new StorageService('', $adapter);

		// Act
		$nonexistentFruit = $storageService->findData('99');

		// Assert
		$this->assertNull($nonexistentFruit);
	}
}