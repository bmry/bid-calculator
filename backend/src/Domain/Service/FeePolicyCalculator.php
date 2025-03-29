<?php
declare(strict_types=1);

namespace Progi\Domain\Service;

use Progi\Domain\Model\Price;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Exception\PolicyNotFoundException;
use Psr\Log\LoggerInterface;
use Money\Money;
use Money\Currency;

/**
 * Calculates fees for a given Price and vehicle type.
 */
class FeePolicyCalculator
{
    /**
     * @param FeePolicyRepositoryInterface $repository Repository to retrieve fee policies.
     * @param LoggerInterface $logger Logger instance.
     */
    public function __construct(
        private FeePolicyRepositoryInterface $repository,
        private LoggerInterface $logger
    ) {}

    /**
     * Calculates the fee breakdown.
     *
     * @param Price $price
     * @param string $vehicleType
     * @return FeeBreakdown
     * @throws PolicyNotFoundException if no fee policy is found.
     */
    public function calculateFees(Price $price, string $vehicleType): FeeBreakdown
    {
        $policy = $this->repository->findByVehicleType($vehicleType);
        if (!$policy) {
            $this->logger->warning("Fee policy not found for vehicle type=$vehicleType");
            throw PolicyNotFoundException::forVehicleType($vehicleType);
        }

        $priceValue = $price->asMoney();

        $basicFee = $priceValue->multiply((string)$policy->baseFeeRate, Money::ROUND_HALF_UP);

        $minBasic = new Money((int)round($policy->baseFeeMin * 100), $priceValue->getCurrency());
        $maxBasic = new Money((int)round($policy->baseFeeMax * 100), $priceValue->getCurrency());

        if ($basicFee->lessThan($minBasic)) {
            $basicFee = $minBasic;
        }
        if ($basicFee->greaterThan($maxBasic)) {
            $basicFee = $maxBasic;
        }

        $specialFee = $priceValue->multiply((string)$policy->specialFeeRate, Money::ROUND_HALF_UP);

        $associationFee = $this->calcAssociationFee($priceValue, $policy->associationTiers);

        $storageFee = new Money((int)round($policy->storageFee * 100), $priceValue->getCurrency());

        $items = [
            new FeeLineItem("BasicBuyerFee", $basicFee),
            new FeeLineItem("SpecialFee", $specialFee),
            new FeeLineItem("AssociationFee", $associationFee),
            new FeeLineItem("StorageFee", $storageFee)
        ];

        $total = $priceValue->add($basicFee)
            ->add($specialFee)
            ->add($associationFee)
            ->add($storageFee);

        $this->logger->info("Calculated fees for $vehicleType with price {$priceValue->getAmount()} cents => total={$total->getAmount()} cents");

        return new FeeBreakdown($items, $total);
    }

    /**
     * Determines the association fee using tier thresholds.
     *
     * @param Money $priceValue
     * @param array<int, array{max: float, fee: float}> $tiers
     * @return Money
     */
    private function calcAssociationFee(Money $priceValue, array $tiers): Money
    {
        $currency = $priceValue->getCurrency();
        $priceFloat = (float)$priceValue->getAmount() / 100;

        foreach ($tiers as $tier) {
            if ($priceFloat <= $tier['max']) {
                $feeCents = (int) round($tier['fee'] * 100);
                return new Money($feeCents, $currency);
            }
        }
        throw new \RuntimeException("No association tier matched price={$priceFloat}");
    }
}
