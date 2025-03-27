<?php
declare(strict_types=1);

namespace Progi\Application\DTO;

use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;

/**
 * Data Transfer Object representing an itemized fee breakdown.
 *
 * All fee amounts and the total are formatted as currency strings (e.g., "$39.80").
 */
class BidFeesDTO
{
    /** @var array<int, array{name: string, amount: string}> */
    public array $items;
    public string $total;

    private function __construct() {}

    /**
     * Creates a DTO from a FeeBreakdown, formatting amounts as currency.
     *
     * @param FeeBreakdown $breakdown
     * @return self
     */
    public static function fromFeeBreakdown(FeeBreakdown $breakdown): self
    {
        $dto = new self();
        $dto->items = array_map(
            fn(FeeLineItem $item) => [
                'name' => $item->name,
                'amount' => self::formatCurrency($item->amount)
            ],
            $breakdown->items()
        );
        $dto->total = self::formatCurrency($breakdown->total());
        return $dto;
    }

    /**
     * Formats a number as a currency string with a dollar symbol and 2 decimals.
     *
     * @param float $amount
     * @return string
     */
    private static function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2, '.', '');
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
