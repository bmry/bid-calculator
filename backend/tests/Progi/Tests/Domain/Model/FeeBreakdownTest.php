<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Money\Money;
use Money\Currency;

/**
 * Unit tests for FeeBreakdown.
 */
class FeeBreakdownTest extends TestCase
{
    public function testStoresItemsAndTotal(): void
    {
        $items = [
            new FeeLineItem("BasicBuyerFee", new Money(5000, new Currency('CAD'))),
            new FeeLineItem("SpecialFee", new Money(2000, new Currency('CAD')))
        ];
        $breakdown = new FeeBreakdown($items, new Money(17000, new Currency('CAD')));
        $this->assertCount(2, $breakdown->items());
        $this->assertEquals(17000, $breakdown->total()->getAmount());
    }
}
