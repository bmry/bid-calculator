<?php
declare(strict_types=1);

namespace Progi\Domain\Service;

use Progi\Domain\Model\Price;
use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Exception\PolicyNotFoundException;
use Progi\Domain\Service\FeeCalculator\FeeCalculatorFactory;
use Psr\Log\LoggerInterface;
use Money\Money;

/**
 * Calculates fees for a given Price and vehicle type.
 */
class FeePolicyCalculator
{
    /**
     * @param FeePolicyRepositoryInterface $repository Repository to retrieve fee policies.
     * @param LoggerInterface $logger Logger instance.
     * @param FeeCalculatorFactory $calculatorFactory Factory for fee calculators.
     */
    public function __construct(
        private FeePolicyRepositoryInterface $repository,
        private LoggerInterface $logger,
        private FeeCalculatorFactory $calculatorFactory
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

        $items = [
            new FeeLineItem("BasicBuyerFee", $this->calculatorFactory->getCalculator('basic')->calculate($priceValue, $policy)),
            new FeeLineItem("SpecialFee", $this->calculatorFactory->getCalculator('special')->calculate($priceValue, $policy)),
            new FeeLineItem("AssociationFee", $this->calculatorFactory->getCalculator('association')->calculate($priceValue, $policy)),
            new FeeLineItem("StorageFee", $this->calculatorFactory->getCalculator('storage')->calculate($priceValue, $policy))
        ];

        $total = $priceValue;
        foreach ($items as $item) {
            $total = $total->add($item->amount());
        }

        $this->logger->info("Calculated fees for $vehicleType with price {$priceValue->getAmount()} cents => total={$total->getAmount()} cents");

        return new FeeBreakdown($items, $total);
    }
}
