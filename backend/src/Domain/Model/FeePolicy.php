<?php
declare(strict_types=1);

namespace Progi\Domain\Model;

/**
 * Contains fee parameters for a vehicle type.
 *
 * @param array<int, array{max: float, fee: float}> $associationTiers The tiers for association fees.
 */
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
