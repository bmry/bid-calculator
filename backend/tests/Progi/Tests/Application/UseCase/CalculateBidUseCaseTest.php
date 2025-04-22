<?php
declare(strict_types=1);

namespace Progi\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorFactory;
use Progi\Domain\Service\FeeCalculator\BasicFeeCalculator;
use Progi\Domain\Service\FeeCalculator\SpecialFeeCalculator;
use Progi\Domain\Service\FeeCalculator\AssociationFeeCalculator;
use Progi\Domain\Service\FeeCalculator\StorageFeeCalculator;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Model\Price;
use Psr\Log\NullLogger;

/**
 * Tests that CalculateBidUseCase returns a valid BidFeesDTO.
 */
class CalculateBidUseCaseTest extends TestCase
{
    private CalculateBidUseCase $useCase;

    protected function setUp(): void
    {
        $repo = $this->createMock(FeePolicyRepositoryInterface::class);
        $repo->method('findByVehicleType')->willReturnCallback(
            fn($type) => $type === 'common'
                ? new FeePolicy(
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
                : null
        );

        $factory = new FeeCalculatorFactory(
            new BasicFeeCalculator(),
            new SpecialFeeCalculator(),
            new AssociationFeeCalculator(),
            new StorageFeeCalculator()
        );

        $calculator = new FeePolicyCalculator($repo, new NullLogger(), $factory);
        $this->useCase = new CalculateBidUseCase($calculator);
    }

    public function testCalculateBidForCommonVehicle(): void
    {
        $result = $this->useCase->execute(398, 'common');
        $this->assertEquals(55076, $result->total()->getAmount());
    }

    public function testCalculateBidForUnknownVehicleType(): void
    {
        $this->expectException(\Progi\Domain\Exception\PolicyNotFoundException::class);
        $this->useCase->execute(1000, 'luxury');
    }
}
