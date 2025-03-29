<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Exception\InvalidPriceException;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\PolicyNotFoundException;
use Psr\Log\NullLogger;
use Money\Money;
use Money\Currency;

/**
 * Unit tests for FeePolicyCalculator covering both normal and error cases.
 */
class FeePolicyCalculatorTest extends TestCase
{
    private FeePolicyCalculator $calc;

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
        $this->calc = new FeePolicyCalculator($repo, new NullLogger());
    }

    public function testCalculateCommon398(): void
    {
        $price = Price::fromFloat(398, 'CAD');
        $breakdown = $this->calc->calculateFees($price, 'common');
        $this->assertEquals(55076, $breakdown->total()->getAmount());
    }

    public function testZeroPriceThrows(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculateFees(Price::fromFloat(0, 'CAD'), 'common');
    }

    public function testUnknownVehicleTypeThrows(): void
    {
        $this->expectException(PolicyNotFoundException::class);
        $this->calc->calculateFees(Price::fromFloat(1000, 'CAD'), 'luxury');
    }
}
