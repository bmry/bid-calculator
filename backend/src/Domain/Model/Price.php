<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

use Progi\Domain\Exception\InvalidPriceException;

/**
 * Represents a price value object ensuring the price is greater than zero.
 */
final class Price
{
    private float $amount;

    /**
     * @param float $amount
     * @throws InvalidPriceException if the amount is not positive.
     */
    private function __construct(float $amount)
    {
        if ($amount <= 0) {
            throw InvalidPriceException::becausePriceMustBePositive($amount);
        }
        $this->amount = $amount;
    }

    /**
     * Factory method to create a Price.
     *
     * @param float $amount
     * @return self
     */
    public static function fromFloat(float $amount): self
    {
        return new self($amount);
    }

    /**
     * Returns the price as a float.
     *
     * @return float
     */
    public function toFloat(): float
    {
        return $this->amount;
    }
}
