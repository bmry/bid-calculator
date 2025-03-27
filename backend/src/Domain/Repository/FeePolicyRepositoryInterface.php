<?php

namespace Progi\Domain\Repository;

use Progi\Domain\Model\FeePolicy;

interface FeePolicyRepositoryInterface
{
    public function findByVehicleType(string $vehicleType): ?FeePolicy;
}
