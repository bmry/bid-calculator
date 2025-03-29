<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;

/**
 * Unit tests for the Price value object.
 */
class PriceTest extends TestCase
{
    public function testCanCreateValidPrice(): void
    {
        $price = Price::fromFloat(100.0, 'CAD');
        $this->assertEquals(10000, $price->asMoney()->getAmount()); // 100.00 CAD = 10000 cents
    }

    public function testZeroPriceThrows(): void
    {
        $this->expectException(InvalidPriceException::class);
        Price::fromFloat(0.0, 'CAD');
    }

    public function testNegativePriceThrows(): void
    {
        $this->expectException(InvalidPriceException::class);
        Price::fromFloat(-10.0, 'CAD');
    }
}
