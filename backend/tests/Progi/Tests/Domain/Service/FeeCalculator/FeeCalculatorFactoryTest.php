<?php
declare(strict_types=1);

namespace Progi\Tests\Domain\Service\FeeCalculator;

use PHPUnit\Framework\TestCase;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorFactory;
use Progi\Domain\Service\FeeCalculator\BasicFeeCalculator;
use Progi\Domain\Service\FeeCalculator\SpecialFeeCalculator;
use Progi\Domain\Service\FeeCalculator\AssociationFeeCalculator;
use Progi\Domain\Service\FeeCalculator\StorageFeeCalculator;
use Progi\Domain\Model\FeePolicy;
use Money\Money;
use Money\Currency;
use Progi\Domain\Model\Price;
use Progi\Domain\Exception\InvalidPriceException;

class FeeCalculatorFactoryTest extends TestCase
{
    private FeeCalculatorFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FeeCalculatorFactory(
            new BasicFeeCalculator(),
            new SpecialFeeCalculator(),
            new AssociationFeeCalculator(),
            new StorageFeeCalculator()
        );
    }

    public function testGetCalculatorReturnsCorrectInstance(): void
    {
        $this->assertInstanceOf(
            BasicFeeCalculator::class,
            $this->factory->getCalculator('basic')
        );

        $this->assertInstanceOf(
            SpecialFeeCalculator::class,
            $this->factory->getCalculator('special')
        );

        $this->assertInstanceOf(
            AssociationFeeCalculator::class,
            $this->factory->getCalculator('association')
        );

        $this->assertInstanceOf(
            StorageFeeCalculator::class,
            $this->factory->getCalculator('storage')
        );
    }

    public function testGetCalculatorThrowsForUnknownType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown calculator type: unknown');
        
        $this->factory->getCalculator('unknown');
    }

    public function testCalculatorsProduceCorrectResults(): void
    {
        $policy = new FeePolicy(
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
        );

        $price = Money::CAD(39800); // $398.00

        // Test basic fee calculation
        $basicFee = $this->factory->getCalculator('basic')->calculate($price, $policy);
        $this->assertEquals(3980, $basicFee->getAmount()); // $39.80

        // Test special fee calculation
        $specialFee = $this->factory->getCalculator('special')->calculate($price, $policy);
        $this->assertEquals(796, $specialFee->getAmount()); // $7.96

        // Test association fee calculation
        $associationFee = $this->factory->getCalculator('association')->calculate($price, $policy);
        $this->assertEquals(500, $associationFee->getAmount()); // $5.00

        // Test storage fee calculation
        $storageFee = $this->factory->getCalculator('storage')->calculate($price, $policy);
        $this->assertEquals(10000, $storageFee->getAmount()); // $100.00
    }

    public function testCreateBasicFeeCalculator(): void
    {
        $calculator = $this->factory->createBasicFeeCalculator();
        $this->assertInstanceOf(BasicFeeCalculator::class, $calculator);
        
        $price = Price::fromFloat(1000, 'CAD');
        $result = $calculator->calculate($price, 0.1, 10, 50);
        $this->assertEquals(100, $result->getAmount());
    }

    public function testCreateSpecialFeeCalculator(): void
    {
        $calculator = $this->factory->createSpecialFeeCalculator();
        $this->assertInstanceOf(SpecialFeeCalculator::class, $calculator);
        
        $price = Price::fromFloat(1000, 'CAD');
        $result = $calculator->calculate($price, 0.02);
        $this->assertEquals(20, $result->getAmount());
    }

    public function testCreateAssociationFeeCalculator(): void
    {
        $calculator = $this->factory->createAssociationFeeCalculator();
        $this->assertInstanceOf(AssociationFeeCalculator::class, $calculator);
        
        $price = Price::fromFloat(1000, 'CAD');
        $result = $calculator->calculate($price, [
            ['max' => 500, 'fee' => 5],
            ['max' => 1000, 'fee' => 10],
            ['max' => 3000, 'fee' => 15],
            ['max' => 999999999, 'fee' => 20]
        ]);
        $this->assertEquals(10, $result->getAmount());
    }

    public function testCreateStorageFeeCalculator(): void
    {
        $calculator = $this->factory->createStorageFeeCalculator();
        $this->assertInstanceOf(StorageFeeCalculator::class, $calculator);
        
        $result = $calculator->calculate(100);
        $this->assertEquals(100, $result->getAmount());
    }

    public function testInvalidPriceThrowsException(): void
    {
        $calculator = $this->factory->createBasicFeeCalculator();
        $this->expectException(InvalidPriceException::class);
        $calculator->calculate(Price::fromFloat(0, 'CAD'), 0.1, 10, 50);
    }
} 