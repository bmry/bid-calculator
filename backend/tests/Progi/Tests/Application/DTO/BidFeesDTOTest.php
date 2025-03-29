<?php
declare(strict_types=1);

namespace Progi\Tests\Application\DTO;

use PHPUnit\Framework\TestCase;
use Progi\Application\DTO\BidFeesDTO;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Money\Money;
use Money\Currency;

/**
 * Unit tests for BidFeesDTO conversion.
 */
class BidFeesDTOTest extends TestCase
{
    public function testFromFeeBreakdown(): void
    {
        $breakdown = new FeeBreakdown(
            items: [
                new FeeLineItem("BasicBuyerFee", new Money(3980, new Currency('CAD'))),
                new FeeLineItem("SpecialFee", new Money(796, new Currency('CAD')))
            ],
            total: new Money(55076, new Currency('CAD'))
        );
        $dto = BidFeesDTO::fromFeeBreakdown($breakdown);
        $this->assertCount(2, $dto->items);
        $this->assertEquals("BasicBuyerFee", $dto->items[0]['name']);
        $this->assertStringContainsString("39.80", $dto->items[0]['amount']);
        $this->assertStringContainsString("550.76", $dto->total);
    }
}
