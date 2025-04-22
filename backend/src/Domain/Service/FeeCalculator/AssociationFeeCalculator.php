<?php
declare(strict_types=1);

namespace Progi\Domain\Service\FeeCalculator;

use Money\Money;
use Progi\Domain\Model\FeePolicy;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorInterface;

final class AssociationFeeCalculator implements FeeCalculatorInterface
{
    public function calculate(Money $priceValue, FeePolicy $policy): Money
    {
        $currency = $priceValue->getCurrency();
        $priceFloat = (float)$priceValue->getAmount() / 100;

        foreach ($policy->associationTiers as $tier) {
            if ($priceFloat <= $tier['max']) {
                $feeCents = (int) round($tier['fee'] * 100);
                return new Money($feeCents, $currency);
            }
        }
        throw new \RuntimeException("No association tier matched price={$priceFloat}");
    }
} 