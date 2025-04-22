<?php
declare(strict_types=1);

namespace Progi\Domain\Service\FeeCalculator;

use Money\Money;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorInterface;

final class StorageFeeCalculator implements FeeCalculatorInterface
{
    public function calculate(Money $priceValue, FeePolicy $policy): Money
    {
        return new Money((int)round($policy->storageFee * 100), $priceValue->getCurrency());
    }
} 