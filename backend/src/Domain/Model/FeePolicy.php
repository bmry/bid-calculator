<?php

namespace Progi\Domain\Model;

final class FeePolicy
{
    public function __construct(
        public float $baseFeeRate,
        public float $baseFeeMin,
        public float $baseFeeMax,
        public float $specialFeeRate,
        public array $associationTiers,
        public float $storageFee
    ) {
    }
}
