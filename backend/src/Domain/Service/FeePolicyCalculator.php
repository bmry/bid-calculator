<?php

namespace Progi\Domain\Service;

use Progi\Domain\Model\Price;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Exception\PolicyNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * Calculates fees for a given price and vehicle type.
 */
class FeePolicyCalculator
{
    /**
     * @param FeePolicyRepositoryInterface $repository Repository to fetch fee policies.
     * @param LoggerInterface $logger PSR-3 logger instance.
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
     * @throws PolicyNotFoundException if no fee policy exists for the vehicle type.
     */
    public function calculateFees(Price $price, string $vehicleType): FeeBreakdown
    {
        $policy = $this->repository->findByVehicleType($vehicleType);
        if (!$policy) {
            $this->logger->warning("Fee policy not found for vehicle type=$vehicleType");
            throw PolicyNotFoundException::forVehicleType($vehicleType);
        }

        $priceValue = $price->toFloat();

        $basicFee = $priceValue * $policy->baseFeeRate;
        $basicFee = max($policy->baseFeeMin, min($basicFee, $policy->baseFeeMax));

        $specialFee = $priceValue * $policy->specialFeeRate;

        $associationFee = $this->calcAssociationFee($priceValue, $policy->associationTiers);

        $storageFee = $policy->storageFee;

        $items = [
            new FeeLineItem("BasicBuyerFee", round($basicFee, 2)),
            new FeeLineItem("SpecialFee", round($specialFee, 2)),
            new FeeLineItem("AssociationFee", (float)$associationFee),
            new FeeLineItem("StorageFee", (float)$storageFee)
        ];

        $total = $priceValue + $basicFee + $specialFee + $associationFee + $storageFee;
        $this->logger->info("Calculated fees for $vehicleType with price $priceValue => total=$total");

        return new FeeBreakdown($items, round($total, 2));
    }

    /**
     * Determines the association fee based on configured tiers.
     *
     * @param float $priceValue
     * @param array<int, array{max: float, fee: float}> $tiers
     * @return float
     */
    private function calcAssociationFee(float $priceValue, array $tiers): float
    {
        foreach ($tiers as $tier) {
            if ($priceValue <= $tier['max']) {
                return (float)$tier['fee'];
            }
        }
        throw new \RuntimeException("No association tier matched price=$priceValue");
    }
}
