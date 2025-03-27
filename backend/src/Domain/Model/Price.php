<?php

namespace Progi\Domain\Model;

use Progi\Domain\Exception\InvalidPriceException;

/**
 * Price is a Value Object ensuring the amount is always > 0.
 */
final class Price
{
    private float $amount;

    private function __construct(float $amount)
    {
        if ($amount <= 0) {
            throw InvalidPriceException::becausePriceMustBePositive($amount);
        }
        $this->amount = $amount;
    }

    public static function fromFloat(float $amount): self
    {
        return new self($amount);
    }

    public function toFloat(): float
    {
        return $this->amount;
    }
}
