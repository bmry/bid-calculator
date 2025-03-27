<?php

namespace Progi\Domain\Exception;

use DomainException;

/**
 * Thrown when someone tries to create a Price <= 0.
 */
class InvalidPriceException extends DomainException
{
    public static function becausePriceMustBePositive(float $amount): self
    {
        return new self(sprintf('Price must be > 0. Got %.2f', $amount));
    }
}
