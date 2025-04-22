<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service\FeeCalculator;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeeCalculator\SpecialFeeCalculator;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;

class SpecialFeeCalculatorTest extends TestCase
{
    private SpecialFeeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new SpecialFeeCalculator();
    }

    public function testCalculateSpecialFee(): void
    {
        $price = Price::fromFloat(1000, 'CAD');
        $result = $this->calculator->calculate($price, 0.02);
        $this->assertEquals(20, $result->getAmount());
    }

    public function testCalculateSpecialFeeWithZeroRate(): void
    {
        $price = Price::fromFloat(1000, 'CAD');
        $result = $this->calculator->calculate($price, 0);
        $this->assertEquals(0, $result->getAmount());
    }

    public function testZeroPriceThrowsException(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calculator->calculate(Price::fromFloat(0, 'CAD'), 0.02);
    }

    public function testNegativePriceThrowsException(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calculator->calculate(Price::fromFloat(-100, 'CAD'), 0.02);
    }
} 