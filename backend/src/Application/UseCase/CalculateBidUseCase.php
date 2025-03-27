<?php

namespace Progi\Application\UseCase;

use Progi\Application\DTO\BidFeesDTO;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Model\Price;

/**
 * The application-level use case: given price + type, compute the fees (DTO).
 */
class CalculateBidUseCase
{
    public function __construct(
        private FeePolicyCalculator $calculator
    ) {
    }

    public function execute(float $priceValue, string $vehicleType): BidFeesDTO
    {
        $price = Price::fromFloat($priceValue);
        $breakdown = $this->calculator->calculateFees($price, $vehicleType);

        return BidFeesDTO::fromFeeBreakdown($breakdown);
    }
}
