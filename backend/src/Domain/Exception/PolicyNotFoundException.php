<?php

namespace Progi\Domain\Exception;

use DomainException;

class PolicyNotFoundException extends DomainException
{
    public static function forVehicleType(string $vehicleType): self
    {
        return new self("No fee policy found for vehicle type: $vehicleType");
    }
}
