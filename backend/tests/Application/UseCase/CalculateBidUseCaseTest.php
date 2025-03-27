<?php

namespace Progi\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Psr\Log\NullLogger;

/**
 * Tests that the UseCase calls the calculator and returns a proper BidFeesDTO.
 */
class CalculateBidUseCaseTest extends TestCase
{
    public function testExecuteReturnsDtoWithItems(): void
    {
        // Mock the calculator
        $calc = $this->createMock(FeePolicyCalculator::class);
        // Suppose the domain returns 2 line items + total=150
        $calc->method('calculateFees')->willReturn(
            new FeeBreakdown(
                items: [
                    new FeeLineItem("BasicFee", 30.0),
                    new FeeLineItem("AssociationFee", 20.0)
                ],
                total: 150.0
            )
        );

        $useCase = new CalculateBidUseCase($calc);

        // Execute
        $dto = $useCase->execute(100, 'common');
        $this->assertCount(2, $dto->items);
        $this->assertEquals('BasicFee', $dto->items[0]['name']);
        $this->assertEquals(30.0, $dto->items[0]['amount']);
        $this->assertEquals(150.0, $dto->total);
    }
}
