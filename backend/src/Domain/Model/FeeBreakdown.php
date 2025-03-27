<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

/**
 * Represents an itemized fee breakdown including the total sum.
 *
 * @param FeeLineItem[] $items An array of fee line items.
 */
final class FeeBreakdown
{
    /**
     * @param FeeLineItem[] $items
     * @param float $total
     */
    public function __construct(
        private array $items,
        private float $total
    ) {}

    /**
     * Returns the fee line items.
     *
     * @return FeeLineItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Returns the total fee.
     *
     * @return float
     */
    public function total(): float
    {
        return $this->total;
    }
}
