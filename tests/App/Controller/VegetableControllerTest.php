<?php

namespace App\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VegetableControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetVegetablesWithNameFilter(): void
    {
        $this->client->request('GET', '/api/vegetables?name=Carrot');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertEquals('Carrot', $data[1]['name']);
    }

    public function testGetVegetablesWithWeightFilter(): void
    {
        $this->client->request('GET', '/api/vegetables?grams=200');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertEquals(200, $data[0]['grams']);
    }

    public function testGetVegetablesWithMinWeightFilter(): void
    {
        $this->client->request('GET', '/api/vegetables?min_grams=150');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifie que le premier légume a un poids >= 150g
        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual(150, $data[1]['grams']);
    }

    public function testGetVegetablesWithUnitFilter(): void
    {
        $this->client->request('GET', '/api/vegetables?unit=kilograms');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifie que l'unité du poids est bien "kilograms"
        $this->assertNotEmpty($data);
        $this->assertIsFloat($data[1]['grams']);
    }

    public function testGetVegetablesWithMultipleFilters(): void
    {
        $this->client->request('GET', '/api/vegetables?name=Carrot&min_grams=150&unit=kilograms');

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est OK
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifie les différents filtres
        $this->assertNotEmpty($data);
        $this->assertEquals('Carrot', $data[1]['name']);
    }

    public function testGetVegetableNotFound(): void
    {
        $this->client->request('GET', '/api/vegetables/9999'); // ID inexistant

        $this->assertResponseStatusCodeSame(404); // Not Found
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Not found', $data['error']);
    }
}
