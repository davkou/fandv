<?php

namespace App\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FruitControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetFruitsWithNameFilter(): void
    {
        $this->client->request('GET', '/api/fruits?name=Apple');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertEquals('Apple', $data[1]['name']);
    }

    public function testGetFruitsWithWeightFilter(): void
    {
        $this->client->request('GET', '/api/fruits?grams=150');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertEquals(150, $data[1]['grams']);
    }

    public function testGetFruitsWithMinWeightFilter(): void
    {
        $this->client->request('GET', '/api/fruits?min_grams=100');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifie que le premier fruit a un poids >= 100g
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual(100, $data[1]['grams']);
    }

    public function testGetFruitsWithUnitFilter(): void
    {
        $this->client->request('GET', '/api/fruits?unit=kilograms');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifie que l'unité du poids est bien "kilograms"
        $this->assertNotEmpty($data);
        $this->assertIsFloat($data[1]['grams']);
    }

    public function testGetFruitsWithMultipleFilters(): void
    {
        $this->client->request('GET', '/api/fruits?name=Apple&min_grams=100&unit=kilograms');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifie les différents filtres
        $this->assertNotEmpty($data);
        $this->assertEquals('Apple', $data[1]['name']);
    }

    public function testGetFruitNotFound(): void
    {
        $this->client->request('GET', '/api/fruits/9999'); // ID inexistant

        $this->assertResponseStatusCodeSame(404); // Not Found
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Not found', $data['error']);
    }

    public function testPostFruitWithInvalidIdType(): void
    {
        // Arrange
        $payload = [
            'id' => '2', // String ici pour tester la validation
            'name' => 'Orange',
            'grams' => 100
        ];

        // Act
        $this->client->request('POST', '/api/fruits', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        // Décoder la réponse JSON pour travailler avec un tableau PHP
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertArrayHasKey('error', $responseContent);
        $this->assertStringContainsString('The type of the "id" attribute for class "App\\Entity\\Fruit" must be one of "int" ("string" given).', $responseContent['error']);
    }
}
