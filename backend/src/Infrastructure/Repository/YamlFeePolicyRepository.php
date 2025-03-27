<?php

namespace Progi\Infrastructure\Repository;

use Symfony\Component\Yaml\Yaml;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;

/**
 * Loads fee policies from config/fee_policies.yaml at runtime.
 */
class YamlFeePolicyRepository implements FeePolicyRepositoryInterface
{
    private array $policies;

    /**
     * @param string $configPath e.g. %kernel.project_dir%/config/fee_policies.yaml
     */
    public function __construct(string $configPath)
    {
        $parsed = Yaml::parseFile($configPath);
        $this->policies = $parsed ?? [];
    }

    public function findByVehicleType(string $vehicleType): ?FeePolicy
    {
        // Lowercase to match keys in the YAML
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
