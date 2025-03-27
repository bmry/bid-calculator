<?php
declare(strict_types=1);

namespace Progi\Domain\Repository;

use Progi\Domain\Model\FeePolicy;

interface FeePolicyRepositoryInterface
{
    /**
     * Retrieves the FeePolicy for a given vehicle type.
     *
     * @param string $vehicleType
     * @return FeePolicy|null Returns null if not found.
     */
    public function findByVehicleType(string $vehicleType): ?FeePolicy;
}
