<?php

namespace Progi\Domain\Service;

use Progi\Domain\Model\Price;
use Progi\Domain\Model\FeeLineItem;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Exception\PolicyNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * Core domain service: calculates all fees for a given Price + vehicle type.
 */
class FeePolicyCalculator
{
    public function __construct(
        private FeePolicyRepositoryInterface $repository,
        private LoggerInterface $logger
    ) {
    }

    public function calculateFees(Price $price, string $vehicleType): FeeBreakdown
    {
        // Attempt to load the FeePolicy from the repository
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

        $items = [];
        $items[] = new FeeLineItem("BasicBuyerFee", round($basicFee, 2));
        $items[] = new FeeLineItem("SpecialFee", round($specialFee, 2));
        $items[] = new FeeLineItem("AssociationFee", (float)$associationFee);
        $items[] = new FeeLineItem("StorageFee", (float)$storageFee);

        $total = $priceValue + $basicFee + $specialFee + $associationFee + $storageFee;
        $this->logger->info("Calculated fees for $vehicleType @ $priceValue => total=$total");

        return new FeeBreakdown($items, round($total, 2));
    }

    /**
     * Loops over the associationTiers to find the matching fee for a given price.
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
