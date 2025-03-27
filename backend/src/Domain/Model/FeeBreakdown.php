<?php

namespace Progi\Domain\Model;

/**
 * A collection of fee line items plus a total sum.
 */
final class FeeBreakdown
{
    /**
     * @param FeeLineItem[] $items
     */
    public function __construct(
        private array $items,
        private float $total
    ) {
    }

    /**
     * @return FeeLineItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): float
    {
        return $this->total;
    }
}
