<?php

namespace Progi\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;

/**
 * Unit tests for the FeeBreakdown domain model,
 * ensuring it stores and returns line items + total.
 */
class FeeBreakdownTest extends TestCase
{
    public function testConstructorStoresItemsAndTotal(): void
    {
        $items = [
            new FeeLineItem('BasicBuyerFee', 50.0),
            new FeeLineItem('SpecialFee', 20.0),
        ];
        $total = 170.0;

        $breakdown = new FeeBreakdown($items, $total);

        $this->assertCount(2, $breakdown->items());
        $this->assertEquals('BasicBuyerFee', $breakdown->items()[0]->name);
        $this->assertEquals(50.0, $breakdown->items()[0]->amount);

        $this->assertEquals(170.0, $breakdown->total());
    }
}
