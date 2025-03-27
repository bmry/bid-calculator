<?php

namespace Progi\Domain\Model;

final class FeeBreakdown
{
    /** @var FeeLineItem[] */
    private array $items;
    private float $total;

    /**
     * @param FeeLineItem[] $items
     */
    public function __construct(array $items, float $total)
    {
        $this->items = $items;
        $this->total = $total;
    }

    /** @return FeeLineItem[] */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): float
    {
        return $this->total;
    }
}
