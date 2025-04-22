<?php
declare(strict_types=1);

namespace Progi\Domain\Service\FeeCalculator;

use Money\Money;
use Progi\Domain\Model\FeePolicy;

interface FeeCalculatorInterface
{
    public function calculate(Money $priceValue, FeePolicy $policy): Money;
} 