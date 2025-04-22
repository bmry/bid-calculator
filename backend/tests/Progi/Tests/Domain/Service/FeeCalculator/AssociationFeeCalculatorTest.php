<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service\FeeCalculator;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeeCalculator\AssociationFeeCalculator;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;

class AssociationFeeCalculatorTest extends TestCase
{
    private AssociationFeeCalculator $calculator;
    private array $tiers;

    protected function setUp(): void
    {
        $this->calculator = new AssociationFeeCalculator();
        $this->tiers = [
            ['max' => 500, 'fee' => 5],
            ['max' => 1000, 'fee' => 10],
            ['max' => 3000, 'fee' => 15],
            ['max' => 999999999, 'fee' => 20]
        ];
    }

    public function testCalculateAssociationFeeForLowPrice(): void
    {
        $price = Price::fromFloat(400, 'CAD');
        $result = $this->calculator->calculate($price, $this->tiers);
        $this->assertEquals(5, $result->getAmount());
    }

    public function testCalculateAssociationFeeForMediumPrice(): void
    {
        $price = Price::fromFloat(800, 'CAD');
        $result = $this->calculator->calculate($price, $this->tiers);
        $this->assertEquals(10, $result->getAmount());
    }

    public function testCalculateAssociationFeeForHighPrice(): void
    {
        $price = Price::fromFloat(2000, 'CAD');
        $result = $this->calculator->calculate($price, $this->tiers);
        $this->assertEquals(15, $result->getAmount());
    }

    public function testCalculateAssociationFeeForVeryHighPrice(): void
    {
        $price = Price::fromFloat(5000, 'CAD');
        $result = $this->calculator->calculate($price, $this->tiers);
        $this->assertEquals(20, $result->getAmount());
    }

    public function testZeroPriceThrowsException(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calculator->calculate(Price::fromFloat(0, 'CAD'), $this->tiers);
    }

    public function testNegativePriceThrowsException(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calculator->calculate(Price::fromFloat(-100, 'CAD'), $this->tiers);
    }

    public function testEmptyTiersReturnsZero(): void
    {
        $price = Price::fromFloat(1000, 'CAD');
        $result = $this->calculator->calculate($price, []);
        $this->assertEquals(0, $result->getAmount());
    }
} 