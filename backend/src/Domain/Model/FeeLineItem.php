<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

/**
 * Represents a single fee line item.
 */
final class FeeLineItem
{
    /**
     * @param string $name Name of the fee (e.g., "BasicBuyerFee").
     * @param float $amount The fee amount.
     */
    public function __construct(
        public string $name,
        public float $amount
    ) {}
}
