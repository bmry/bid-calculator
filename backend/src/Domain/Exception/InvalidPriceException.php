<?php
declare(strict_types=1);

namespace Progi\Domain\Exception;

use DomainException;

/**
 * Exception thrown when a Price is less than or equal to zero.
 */
class InvalidPriceException extends DomainException
{
    /**
     * Creates an exception indicating the price must be positive.
     *
     * @param float $amount
     * @return self
     */
    public static function becausePriceMustBePositive(float $amount): self
    {
        return new self(sprintf('Price must be > 0. Got %.2f', $amount));
    }
}
