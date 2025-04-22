<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service\FeeCalculator;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeeCalculator\StorageFeeCalculator;
use Progi\Domain\Model\Price;

class StorageFeeCalculatorTest extends TestCase
{
    private StorageFeeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new StorageFeeCalculator();
    }

    public function testCalculateStorageFee(): void
    {
        $result = $this->calculator->calculate(100);
        $this->assertEquals(100, $result->getAmount());
    }

    public function testCalculateZeroStorageFee(): void
    {
        $result = $this->calculator->calculate(0);
        $this->assertEquals(0, $result->getAmount());
    }

    public function testCalculateNegativeStorageFee(): void
    {
        $result = $this->calculator->calculate(-100);
        $this->assertEquals(-100, $result->getAmount());
    }
} 