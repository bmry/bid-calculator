<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service\FeeCalculator;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeeCalculator\BasicFeeCalculator;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;

class BasicFeeCalculatorTest extends TestCase
{
    private BasicFeeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new BasicFeeCalculator();
    }

    public function testCalculateBasicFee(): void
    {
        $price = Price::fromFloat(1000, 'CAD');
        $result = $this->calculator->calculate($price, 0.1, 10, 50);
        $this->assertEquals(100, $result->getAmount());
    }

    public function testCalculateBasicFeeWithMinLimit(): void
    {
        $price = Price::fromFloat(50, 'CAD');
        $result = $this->calculator->calculate($price, 0.1, 10, 50);
        $this->assertEquals(10, $result->getAmount());
    }

    public function testCalculateBasicFeeWithMaxLimit(): void
    {
        $price = Price::fromFloat(1000, 'CAD');
        $result = $this->calculator->calculate($price, 0.1, 10, 50);
        $this->assertEquals(50, $result->getAmount());
    }

    public function testZeroPriceThrowsException(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calculator->calculate(Price::fromFloat(0, 'CAD'), 0.1, 10, 50);
    }

    public function testNegativePriceThrowsException(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calculator->calculate(Price::fromFloat(-100, 'CAD'), 0.1, 10, 50);
    }
} 