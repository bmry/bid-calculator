<?php
declare(strict_types=1);

namespace Progi\Domain\Service\FeeCalculator;

use Money\Money;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorInterface;

final class SpecialFeeCalculator implements FeeCalculatorInterface
{
    public function calculate(Money $priceValue, FeePolicy $policy): Money
    {
        return $priceValue->multiply((string)$policy->specialFeeRate, Money::ROUND_HALF_UP);
    }
} 