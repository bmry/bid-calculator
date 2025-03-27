<?php

namespace Progi\Domain\Model;

/**
 * Represents one fee in the itemized breakdown,
 * e.g. "BasicBuyerFee => 39.80"
 */
final class FeeLineItem
{
    public function __construct(
        public string $name,
        public float $amount
    ) {}
}
