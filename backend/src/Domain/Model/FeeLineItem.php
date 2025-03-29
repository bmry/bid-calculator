<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

use Money\Money;

/**
 * Represents a single fee line item.
 */
final class FeeLineItem
{
    /**
     * @param string $name The fee name.
     * @param Money $amount The fee amount as a Money object.
     */
    public function __construct(
        private string $name,
        private Money $amount
    ) {}

    /**
     * Returns the fee name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the fee amount.
     *
     * @return Money
     */
    public function amount(): Money
    {
        return $this->amount;
    }
}
