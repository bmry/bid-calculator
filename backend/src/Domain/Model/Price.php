<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

use Progi\Domain\Exception\InvalidPriceException;
use Money\Money;
use Money\Currency;

/**
 * Represents a price as a Money object.
 *
 * This value object enforces that the price is greater than zero.
 */
final class Price
{
    private Money $money;

    /**
     * @param Money $money
     */
    private function __construct(Money $money)
    {
        if ($money->isZero() || $money->isNegative()) {
            throw InvalidPriceException::becausePriceMustBePositive((float) $money->getAmount() / 100);
        }
        $this->money = $money;
    }

    /**
     * Factory method to create a Price from a float amount.
     * The amount is expected in dollars, and it is converted to cents.
     *
     * @param float $amount
     * @param string $currency ISO 4217 code (default: CAD)
     * @return self
     */
    public static function fromFloat(float $amount, string $currency = 'CAD'): self
    {
        $cents = (int) round($amount * 100);
        return new self(new Money($cents, new Currency($currency)));
    }

    /**
     * Returns the underlying Money object.
     *
     * @return Money
     */
    public function asMoney(): Money
    {
        return $this->money;
    }
}
