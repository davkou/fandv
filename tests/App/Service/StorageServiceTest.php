<?php

namespace App\Tests\App\Service;

use App\Service\StorageService;
use App\Storage\InMemoryStorageAdapter;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    public function testReceivingRequest(): void
    {
	    // Arrange
	    $request = file_get_contents('request.json');
	    $adapter = new InMemoryStorageAdapter(); // Crée un adaptateur

	    // Act
	    $storageService = new StorageService($request, $adapter); // Passe l'adaptateur au constructeur

	    // Assert
	    $this->assertNotEmpty($storageService->getRequest());
	    $this->assertIsString($storageService->getRequest());
    }
}
