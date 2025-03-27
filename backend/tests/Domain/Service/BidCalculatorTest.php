<?php

namespace Progi\Tests\Domain\Service;

use App\Domain\Service\BidCalculator;
use App\Domain\Model\VehicleType;
use PHPUnit\Framework\TestCase;

class BidCalculatorTest extends TestCase
{
    private BidCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new BidCalculator();
    }

    public function testCalculateFeesCommon(): void
    {
        $result = $this->calculator->calculateFees(398, VehicleType::Common);

        $this->assertEquals(39.8, $result['basicBuyerFee']);
        $this->assertEquals(7.96, $result['specialFee']);
        $this->assertEquals(5, $result['associationFee']);
        $this->assertEquals(100, $result['storageFee']);
        $this->assertEquals(550.76, $result['total']);
    }

    public function testCalculateFeesLuxury(): void
    {
        $result = $this->calculator->calculateFees(1800, VehicleType::Luxury);

        $this->assertEquals(180, $result['basicBuyerFee']); // 10% => 180, but max=200 is fine
        $this->assertEquals(72, $result['specialFee']);     // 4%
        $this->assertEquals(15, $result['associationFee']);
        $this->assertEquals(100, $result['storageFee']);
        $this->assertEquals(2167, $result['total']);
    }
}
