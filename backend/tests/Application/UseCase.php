<?php

namespace Progi\Tests\Application\UseCase;

use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Domain\Service\BidCalculator;
use PHPUnit\Framework\TestCase;

class CalculateBidUseCaseTest extends TestCase
{
    public function testExecuteCommon(): void
    {
        $useCase = new CalculateBidUseCase(new BidCalculator());
        $dto = $useCase->execute(1000, 'Common');

        $this->assertEquals(50, $dto->basicBuyerFee);    // 10% => 100, but capped at 50
        $this->assertEquals(20, $dto->specialFee);       // 2%
        $this->assertEquals(10, $dto->associationFee);
        $this->assertEquals(100, $dto->storageFee);
        $this->assertEquals(1180, $dto->total);
    }
}
