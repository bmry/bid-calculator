<?php
declare(strict_types=1);

namespace Progi\Tests\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class CalculateBidControllerTest extends WebTestCase
{
    /**
     * Test fee calculations for common vehicles.
     *
     * @dataProvider commonVehicleProvider
     */
    public function testCommonVehicleFees(float $price, array $expectedFees): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/v1/bid/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => $price,
                'type' => 'common'
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('total', $data);

        // Check each expected fee line item
        foreach ($expectedFees as $feeName => $expectedValue) {
            $found = false;
            foreach ($data['items'] as $item ) {
                if ($item['name'] === $feeName) {
                    $found = true;
                    $this->assertEquals($expectedValue, $item['amount'], "Fee $feeName did not match.");
                    break;
                }
            }

            if ($feeName === 'Total') {
                continue;
            }
            $this->assertTrue($found, "Fee $feeName not found in response.");
        }

        // Also check total
        $this->assertEquals($expectedFees['Total'], $data['total'], "Total did not match.");
    }

    /**
     * Data provider for common vehicles.
     *
     * @return array
     */
    public function commonVehicleProvider(): array
    {
        return [
            'price 398.00' => [
                398.00,
                [
                    'BasicBuyerFee'   => '$39.80',
                    'SpecialFee'      => '$7.96',
                    'AssociationFee'  => '$5.00',
                    'StorageFee'      => '$100.00',
                    'Total'           => '$550.76',
                ]
            ],
            'price 501.00' => [
                501.00,
                [
                    'BasicBuyerFee'   => '$50.00',
                    'SpecialFee'      => '$10.02',
                    'AssociationFee'  => '$10.00',
                    'StorageFee'      => '$100.00',
                    'Total'           => '$671.02',
                ]
            ],
            'price 57.00' => [
                57.00,
                [
                    'BasicBuyerFee'   => '$10.00',
                    'SpecialFee'      => '$1.14',
                    'AssociationFee'  => '$5.00',
                    'StorageFee'      => '$100.00',
                    'Total'           => '$173.14',
                ]
            ],
            'price 1100.00' => [
                1100.00,
                [
                    'BasicBuyerFee'   => '$50.00',
                    'SpecialFee'      => '$22.00',
                    'AssociationFee'  => '$15.00',
                    'StorageFee'      => '$100.00',
                    'Total'           => '$1287.00',
                ]
            ],
        ];
    }

    /**
     * Test fee calculations for luxury vehicles.
     *
     * @dataProvider luxuryVehicleProvider
     */
    public function testLuxuryVehicleFees(float $price, array $expectedFees): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/v1/bid/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => $price,
                'type' => 'luxury'
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('total', $data);

        foreach ($expectedFees as $feeName => $expectedValue) {
            $found = false;
            foreach ($data['items'] as $item) {
                if ($item['name'] === $feeName && $feeName !== 'Total') {
                    $found = true;
                    $this->assertEquals($expectedValue, $item['amount'], "Fee $feeName did not match.");
                    break;
                }
            }

            if ($feeName === 'Total') {
                continue;
            }

            $this->assertTrue($found, "Fee $feeName not found in response.");
        }

        $this->assertEquals($expectedFees['Total'], $data['total'], "Total did not match.");
    }

    /**
     * Data provider for luxury vehicles.
     *
     * @return array
     */
    public function luxuryVehicleProvider(): array
    {
        return [
            'price 1800.00' => [
                1800.00,
                [
                    'BasicBuyerFee'   => '$180.00',
                    'SpecialFee'      => '$72.00',
                    'AssociationFee'  => '$15.00',
                    'StorageFee'      => '$100.00',
                    'Total'           => '$2167.00',
                ]
            ],
            'price 1000000.00' => [
                1000000.00,
                [
                    'BasicBuyerFee'   => '$200.00',
                    'SpecialFee'      => '$40000.00',
                    'AssociationFee'  => '$20.00',
                    'StorageFee'      => '$100.00',
                    'Total'           => '$1040320.00',
                ]
            ],
        ];
    }
}
