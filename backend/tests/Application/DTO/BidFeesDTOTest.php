<?php
declare(strict_types=1);

namespace Progi\Tests\Application\DTO;

use PHPUnit\Framework\TestCase;
use Progi\Application\DTO\BidFeesDTO;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;

class BidFeesDTOTest extends TestCase
{
    public function testFromFeeBreakdown(): void
    {
        $breakdown = new FeeBreakdown(
            items: [
                new FeeLineItem("BasicBuyerFee", 50.0),
                new FeeLineItem("SpecialFee", 20.0)
            ],
            total: 150.0
        );
        $dto = BidFeesDTO::fromFeeBreakdown($breakdown);
        $this->assertCount(2, $dto->items);
        $this->assertEquals("BasicBuyerFee", $dto->items[0]['name']);
        // Verify the amounts are formatted as currency (e.g., "$50.00")
        $this->assertEquals('$50.00', $dto->items[0]['amount']);
        $this->assertEquals('$150.00', $dto->total);
    }
}
