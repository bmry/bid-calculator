<?php

namespace App\Infrastructure\Repository;

use Symfony\Component\Yaml\Yaml;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;

class YamlFeePolicyRepository implements FeePolicyRepositoryInterface
{
    private array $policies;

    public function __construct(string $configPath)
    {
        $parsed = Yaml::parseFile($configPath);
        $this->policies = $parsed ?? [];
    }

    public function findByVehicleType(string $vehicleType): ?FeePolicy
    {
        $vehicleType = strtolower($vehicleType);
        if (!isset($this->policies[$vehicleType])) {
            return null;
        }

        $data = $this->policies[$vehicleType];

        return new FeePolicy(
            baseFeeRate:      $data['basicFee']['baseRate'],
            baseFeeMin:       $data['basicFee']['min'],
            baseFeeMax:       $data['basicFee']['max'],
            specialFeeRate:   $data['specialFeeRate'],
            associationTiers: $data['associationTiers'],
            storageFee:       $data['storageFee']
        );
    }
}
