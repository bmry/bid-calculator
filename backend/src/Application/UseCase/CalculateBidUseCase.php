<?php
declare(strict_types=1);

namespace Progi\Application\UseCase;

use Progi\Application\DTO\BidFeesDTO;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Model\Price;

class CalculateBidUseCase
{
    public function __construct(
        private FeePolicyCalculator $calculator
    ) {}

    /**
     * Executes the bid fee calculation and returns a DTO.
     *
     * @param float $priceValue
     * @param string $vehicleType
     * @return BidFeesDTO
     */
    public function execute(float $priceValue, string $vehicleType): BidFeesDTO
    {
        $priceVO = Price::fromFloat($priceValue);
        $breakdown = $this->calculator->calculateFees($priceVO, $vehicleType);
        return BidFeesDTO::fromFeeBreakdown($breakdown);
    }
}
