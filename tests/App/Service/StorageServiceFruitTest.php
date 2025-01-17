<?php

namespace App\Tests\App\Service;

use App\Service\Search\SearchAdapter;
use App\Service\StorageService;
use App\Storage\InMemoryStorageAdapter;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class StorageServiceFruitTest extends TestCase
{
    private StorageService $storageService;
    private InMemoryStorageAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new InMemoryStorageAdapter(new SearchAdapter());
        $this->storageService = new StorageService($this->adapter);
    }

    public function testSaveAndRetrieveSingleFruit(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Apple',
            'grams' => 150,
        ];

        $this->storageService->saveData($fruitData, 'fruits');
        $retrievedFruit = $this->storageService->findData(1, 'fruits');

        $this->assertNotNull($retrievedFruit);
        $this->assertSame('Apple', $retrievedFruit['name']);
        $this->assertSame(150, $retrievedFruit['grams']);
    }

    public function testRetrieveAllFruits(): void
    {
        $this->storageService->saveData(['id' => 0, 'name' => 'Apple', 'grams' => 150], 'fruits');
        $this->storageService->saveData(['id' => 1, 'name' => 'Banana', 'grams' => 200], 'fruits');

        $allFruits = $this->storageService->getAllData('fruits', []);

        $this->assertCount(2, $allFruits);
        $this->assertSame('Apple', $allFruits[0]['name']);
        $this->assertSame('Banana', $allFruits[1]['name']);
    }

    public function testHandleNonexistentFruit(): void
    {
        $nonexistentFruit = $this->storageService->findData(99, 'fruits');
        $this->assertNull($nonexistentFruit);
    }

    public function testSaveFruitWithInvalidIdType(): void
    {
        $fruitData = [
            'id' => '1',
            'name' => 'Apple',
            'grams' => 150,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "id" key and it must be an integer.');

        $this->storageService->saveData($fruitData, 'fruits');
    }

    public function testSaveFruitWithInvalidGrams(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Apple',
            'grams' => -50,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "grams" key and it must be greater than or equal to 0.');

        $this->storageService->saveData($fruitData, 'fruits');
    }

    public function testSaveFruitWithInvalidName(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 5454,
            'grams' => 10,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "name" key and it must be a string and not blank!.');

        $this->storageService->saveData($fruitData, 'fruits');
    }

    public function testSaveFruitWithInvalidBlankName(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => '',
            'grams' => 10,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "name" key and it must be a string and not blank!.');

        $this->storageService->saveData($fruitData, 'fruits');
    }

    public function testSaveFruitWithMissingGrams(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Apple',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must contain a valid "grams" key and it must be greater than or equal to 0.');

        $this->storageService->saveData($fruitData, 'fruits');
    }
}
