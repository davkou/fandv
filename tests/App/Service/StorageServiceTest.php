<?php

namespace App\Tests\App\Service;

use App\Service\Search\SearchAdapter;
use App\Service\StorageService;
use App\Storage\InMemoryStorageAdapter;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    private StorageService $storageService;

    protected function setUp(): void
    {
        // Init StorageService
        $adapter = new InMemoryStorageAdapter(new SearchAdapter());
        $this->storageService = new StorageService($adapter);
    }

    public function testReceivingRequest(): void
    {
        // Arrange
        $data = [
            'type' => 'fruit',
            'id' => 1,
            'name' => 'Apple',
            'grams' => 150,
        ];

        // Act
        $this->storageService->saveData($data, 'fruits');
        $retrievedData = $this->storageService->findData(1, 'fruits');

        // Assert
        $this->assertNotNull($retrievedData);
        $this->assertSame('Apple', $retrievedData['name']);
    }
}
