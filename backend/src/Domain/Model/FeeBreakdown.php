<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

use Money\Money;

/**
 * Represents an itemized fee breakdown along with a total.
 *
 * @param FeeLineItem[] $items An array of fee line items.
 */
final class FeeBreakdown
{
    /**
     * @param FeeLineItem[] $items
     * @param Money $total The total fee as a Money object.
     */
    public function __construct(
        private array $items,
        private Money $total
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
     * @return Money
     */
    public function total(): Money
    {
        return $this->total;
    }
}
