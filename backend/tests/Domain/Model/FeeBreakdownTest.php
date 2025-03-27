<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;

class FeeBreakdownTest extends TestCase
{
    public function testStoresItemsAndTotal(): void
    {
        $items = [
            new FeeLineItem("BasicBuyerFee", 50.0),
            new FeeLineItem("SpecialFee", 20.0)
        ];
        $breakdown = new FeeBreakdown($items, 170.0);
        $this->assertCount(2, $breakdown->items());
        $this->assertEquals(170.0, $breakdown->total());
    }
}
