<?php

namespace Progi\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;

/**
 * Unit tests for the Price Value Object.
 */
class PriceTest extends TestCase
{
    public function testCanCreateValidPrice(): void
    {
        $price = Price::fromFloat(100.0);
        $this->assertEquals(100.0, $price->toFloat());
    }

    public function testZeroPriceThrows(): void
    {
        $this->expectException(InvalidPriceException::class);
        Price::fromFloat(0.0);
    }

    public function testNegativePriceThrows(): void
    {
        $this->expectException(InvalidPriceException::class);
        Price::fromFloat(-50.0);
    }
}
