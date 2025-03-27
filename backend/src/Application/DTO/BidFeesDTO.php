<?php

namespace Progi\Application\DTO;

use Progi\Domain\Model\FeeBreakdown;
use Progi\Domain\Model\FeeLineItem;

/**
 * A flexible DTO returning line items + total.
 * If new fees are added, they appear as additional line items automatically.
 */
class BidFeesDTO
{
    /**
     * @var array<int, array{name:string, amount: float}>
     */
    public array $items;
    public float $total;

    private function __construct() {}

    /**
     * Builds the DTO from a FeeBreakdown in the domain.
     */
    public static function fromFeeBreakdown(FeeBreakdown $breakdown): self
    {
        $dto = new self();

        // Convert line items into an array shape
        $dto->items = array_map(
            fn (FeeLineItem $item) => [
                'name' => $item->name,
                'amount' => $item->amount
            ],
            $breakdown->items()
        );

        $dto->total = $breakdown->total();
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total
        ];
    }
}
