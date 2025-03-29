<?php
declare(strict_types=1);

namespace Progi\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;
use Psr\Log\NullLogger;
use Money\Money;
use Money\Currency;

/**
 * Tests that CalculateBidUseCase returns a valid BidFeesDTO.
 */
class CalculateBidUseCaseTest extends TestCase
{
    public function testExecuteReturnsDto(): void
    {
        $repo = $this->createMock(FeePolicyRepositoryInterface::class);
        $repo->method('findByVehicleType')->willReturn(
            new FeePolicy(
                baseFeeRate: 0.1,
                baseFeeMin: 10,
                baseFeeMax: 50,
                specialFeeRate: 0.02,
                associationTiers: [
                    ['max' => 500, 'fee' => 5],
                    ['max' => 1000, 'fee' => 10],
                    ['max' => 3000, 'fee' => 15],
                    ['max' => 999999999, 'fee' => 20]
                ],
                storageFee: 100
            )
        );
        $calculator = new FeePolicyCalculator($repo, new NullLogger());
        $useCase = new CalculateBidUseCase($calculator);

        $dto = $useCase->execute(398, 'common');
        $this->assertNotEmpty($dto->items);
        $this->assertStringContainsString("550.76", $dto->total);
    }
}
