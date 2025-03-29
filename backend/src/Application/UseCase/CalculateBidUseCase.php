<?php
declare(strict_types=1);

namespace Progi\Application\UseCase;

use Progi\Application\DTO\BidFeesDTO;
use Progi\Domain\Service\FeePolicyCalculator;
use Progi\Domain\Model\Price;

/**
 * Use case for calculating bid fees.
 */
class CalculateBidUseCase
{
    public function __construct(
        private FeePolicyCalculator $calculator
    ) {}

    /**
     * Executes the fee calculation and returns a BidFeesDTO.
     *
     * @param float $priceValue Price in dollars.
     * @param string $vehicleType
     * @return BidFeesDTO
     */
    public function execute(float $priceValue, string $vehicleType): BidFeesDTO
    {
        $priceVO = Price::fromFloat($priceValue, 'CAD');
        $breakdown = $this->calculator->calculateFees($priceVO, $vehicleType);
        return BidFeesDTO::fromFeeBreakdown($breakdown);
    }
}
