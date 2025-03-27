<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\PolicyNotFoundException;
use Psr\Log\NullLogger;

class FeePolicyCalculatorTest extends TestCase
{
    private FeePolicyCalculator $calc;

    protected function setUp(): void
    {
        // Create a mock repository that returns a policy for "common" and null for others.
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
        $this->calc = new FeePolicyCalculator($repo, new NullLogger());
    }

    public function testCalculateCommon398(): void
    {
        $price = Price::fromFloat(398);
        $breakdown = $this->calc->calculateFees($price, 'common');
        // Expected total: 398 + (398*0.1=39.8) + (398*0.02=7.96) + 5 + 100 = 550.76
        $this->assertEquals(550.76, $breakdown->total());
    }

    public function testZeroPriceThrows(): void
    {
        $this->expectException(\Progi\Domain\Exception\InvalidPriceException::class);
        $this->calc->calculateFees(Price::fromFloat(0), 'common');
    }

    public function testUnknownVehicleTypeThrows(): void
    {
        $this->expectException(PolicyNotFoundException::class);
        $this->calc->calculateFees(Price::fromFloat(1000), 'luxury');
    }
}
