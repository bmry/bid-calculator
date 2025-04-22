<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

use Progi\Domain\Exception\InvalidPriceException;
use Money\Money;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Currencies\ISOCurrencies;
use NumberFormatter;

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

    /**
     * Formats the price according to the specified locale.
     *
     * @param string $locale The locale to use for formatting (default: en_CA)
     * @return string The formatted price
     */
    public function format(string $locale = 'en_CA'): string
    {
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $currencies = new ISOCurrencies();
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
        return $moneyFormatter->format($this->money);
    }

    /**
     * Returns the amount in dollars (not cents).
     *
     * @return float
     */
    public function getAmount(): float
    {
        return (float) $this->money->getAmount() / 100;
    }

    /**
     * Returns the currency code.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->money->getCurrency()->getCode();
    }
}
