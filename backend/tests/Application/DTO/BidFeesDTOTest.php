<?php

namespace Progi\Tests\Application\DTO;

use PHPUnit\Framework\TestCase;
use Progi\Application\DTO\BidFeesDTO;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;

/**
 * Ensures BidFeesDTO correctly transforms a FeeBreakdown to a flexible array structure.
 */
class BidFeesDTOTest extends TestCase
{
    public function testFromFeeBreakdown(): void
    {
        // Suppose the domain gave us 2 line items + total=180
        $breakdown = new FeeBreakdown(
            items: [
                new FeeLineItem("BasicBuyerFee", 50.0),
                new FeeLineItem("SpecialFee", 20.0),
            ],
            total: 180.0
        );

        $dto = BidFeesDTO::fromFeeBreakdown($breakdown);
        $this->assertCount(2, $dto->items, "Should have 2 line items");
        $this->assertEquals("BasicBuyerFee", $dto->items[0]['name']);
        $this->assertEquals(50.0, $dto->items[0]['amount']);
        $this->assertEquals(180.0, $dto->total);

        // Confirm the toArray() shape
        $arrayData = $dto->toArray();
        $this->assertArrayHasKey('items', $arrayData);
        $this->assertArrayHasKey('total', $arrayData);
        $this->assertEquals(180.0, $arrayData['total']);
        $this->assertEquals("SpecialFee", $arrayData['items'][1]['name']);
    }
}
