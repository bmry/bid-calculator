<?php

namespace Progi\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;
use Progi\Domain\Exception\PolicyNotFoundException;
use Psr\Log\NullLogger;

/**
 * Covers edge cases (price=0, negative, boundary tiers).
 */
class FeePolicyCalculatorTest extends TestCase
{
    private FeePolicyCalculator $calc;
    private FeePolicyRepositoryInterface $repo;

    protected function setUp(): void
    {
        // Mock repository that returns a "common" policy, or null if type != "common"
        $this->repo = $this->createMock(FeePolicyRepositoryInterface::class);
        $this->repo->method('findByVehicleType')
            ->willReturnCallback(fn($type) => $type === 'common'
                ? new FeePolicy(
                    baseFeeRate: 0.1,
                    baseFeeMin: 10,
                    baseFeeMax: 50,
                    specialFeeRate: 0.02,
                    associationTiers: [
                        ['max' => 500, 'fee' => 5],
                        ['max' => 1000, 'fee' => 10],
                        ['max' => 3000, 'fee' => 15],
                        ['max' => 999999999, 'fee' => 20],
                    ],
                    storageFee: 100
                )
                : null
            );

        $this->calc = new FeePolicyCalculator($this->repo, new NullLogger());
    }

    public function testCalculateCommon398(): void
    {
        // A typical scenario: 398 => association=5, clamp basic fee=39.8 => 50 or 39.8?
        // Actually, 10% of 398 => 39.8, but min=10, max=50 => 39.8 is within => so 39.8
        $price = Price::fromFloat(398);
        $breakdown = $this->calc->calculateFees($price, 'common');

        // Basic => 39.8
        // Special => 398*0.02 => 7.96
        // Association => 5
        // Storage => 100
        // Price => 398
        // total => 398 + 39.8 + 7.96 + 5 + 100 => 550.76
        $this->assertEquals(550.76, $breakdown->total());
    }

    public function testCalculateAt500Bound(): void
    {
        // Price=500 => association=5
        $price = Price::fromFloat(500);
        $breakdown = $this->calc->calculateFees($price, 'common');
        // Basic => 10% => 50 => within [10,50]
        // Special => 10
        // Assoc => 5
        // Storage => 100
        // total => 665
        $this->assertEquals(665, $breakdown->total());
    }

    public function testCalculateJustOver500Tier(): void
    {
        // Price=501 => association=10
        $price = Price::fromFloat(501);
        $breakdown = $this->calc->calculateFees($price, 'common');
        // Basic => clamp to 50? Actually 501*0.1=50.1 => clamp to 50?? min=10 max=50 => yes => 50
        // Special => 501*0.02 => 10.02
        // Assoc => 10
        // Storage => 100
        // total => 501 + 50 + 10.02 + 10 + 100 => 671.02
        $this->assertEquals(671.02, $breakdown->total());
    }

    public function testPriceZeroThrows(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculateFees(Price::fromFloat(0), 'common');
    }

    public function testNegativePrice(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculateFees(Price::fromFloat(-50), 'common');
    }

    public function testUnknownVehicleType(): void
    {
        // The mock returns null for anything not 'common'
        $this->expectException(PolicyNotFoundException::class);
        $this->calc->calculateFees(Price::fromFloat(1000), 'luxury');
    }
}
