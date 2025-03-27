<?php
declare(strict_types=1);

namespace Progi\Infrastructure\Repository;

use Symfony\Component\Yaml\Yaml;
use Progi\Domain\Repository\FeePolicyRepositoryInterface;
use Progi\Domain\Model\FeePolicy;

/**
 * Retrieves fee policies from a YAML configuration file.
 */
class YamlFeePolicyRepository implements FeePolicyRepositoryInterface
{
    private array $policies;

    /**
     * @param string $configPath Path to fee_policies.yaml.
     */
    public function __construct(string $configPath)
    {
        $parsed = Yaml::parseFile($configPath);
        $this->policies = $parsed ?? [];
    }

    /**
     * {@inheritdoc}
     */
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
