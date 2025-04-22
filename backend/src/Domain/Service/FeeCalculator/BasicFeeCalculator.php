<?php
declare(strict_types=1);

namespace Progi\Domain\Service\FeeCalculator;

use Money\Money;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorInterface;

final class BasicFeeCalculator implements FeeCalculatorInterface
{
    public function calculate(Money $priceValue, FeePolicy $policy): Money
    {
        $basicFee = $priceValue->multiply((string)$policy->baseFeeRate, Money::ROUND_HALF_UP);
        $minBasic = new Money((int)round($policy->baseFeeMin * 100), $priceValue->getCurrency());
        $maxBasic = new Money((int)round($policy->baseFeeMax * 100), $priceValue->getCurrency());

        if ($basicFee->lessThan($minBasic)) {
            return $minBasic;
        }
        if ($basicFee->greaterThan($maxBasic)) {
            return $maxBasic;
        }
        return $basicFee;
    }
} 