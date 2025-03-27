<?php

namespace Progi\Domain\Model;

/**
 * Holds the numeric rules for a given vehicle type.
 */
final class FeePolicy
{
    /**
     * @param array<int, array{max: float, fee: float}> $associationTiers
     */
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
