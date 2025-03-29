<?php
declare(strict_types=1);

namespace Progi\Application\DTO;

use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use NumberFormatter;

/**
 * Data Transfer Object representing an itemized fee breakdown.
 *
 * All fee amounts and the total are formatted as Canadian Dollar strings (e.g., "CA$550.76").
 */
class BidFeesDTO
{
    /** @var array<int, array{name: string, amount: string}> */
    public array $items;
    public string $total;

    private function __construct() {}

    /**
     * Creates a DTO from a FeeBreakdown.
     *
     * @param FeeBreakdown $breakdown
     * @return self
     */
    public static function fromFeeBreakdown(FeeBreakdown $breakdown): self
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = new NumberFormatter('en_CA', NumberFormatter::CURRENCY);
        $formatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        $dto = new self();
        $dto->items = array_map(
            fn(FeeLineItem $item) => [
                'name' => $item->name(),
                'amount' => $formatter->format($item->amount())
            ],
            $breakdown->items()
        );
        $dto->total = $formatter->format($breakdown->total());
        return $dto;
    }

    /**
     * Converts the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total
        ];
    }
}
