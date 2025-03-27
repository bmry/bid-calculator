<?php

namespace Progi\Tests\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Integration test for the Controller.
 * Makes real HTTP requests to /api/bid/calculate in memory.
 */
class CalculateBidControllerTest extends WebTestCase
{
    public function testValidCommonRequest(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/bid/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => 398,
                'type' => 'common'
            ])
        );

        // 200 success
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        // Data should have "items" => array, "total" => float
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('total', $data);

        // Checking approximate total if the real config matches 398 => 550.76
        $this->assertEquals(550.76, $data['total']);
    }

    public function testZeroPriceThrows400(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/bid/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => 0,
                'type' => 'common'
            ])
        );

        // Expect 400
        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testUnknownVehicleType(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/bid/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => 500,
                'type' => 'exotic' // not in config => 400
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
