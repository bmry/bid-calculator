<?php

namespace Progi\Domain\Repository;

use Progi\Domain\Model\FeePolicy;

/**
 * A contract for loading FeePolicy data, e.g. from YAML, DB, or others.
 */
interface FeePolicyRepositoryInterface
{
    public function findByVehicleType(string $vehicleType): ?FeePolicy;
}
