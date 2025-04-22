<?php
declare(strict_types=1);

namespace Progi\Domain\Service\FeeCalculator;

use Money\Money;
use Progi\Domain\Model\FeePolicy;

final class FeeCalculatorFactory
{
    /** @var array<string, FeeCalculatorInterface> */
    private array $calculators;

    public function __construct(
        BasicFeeCalculator $basicFeeCalculator,
        SpecialFeeCalculator $specialFeeCalculator,
        AssociationFeeCalculator $associationFeeCalculator,
        StorageFeeCalculator $storageFeeCalculator
    ) {
        $this->calculators = [
            'basic' => $basicFeeCalculator,
            'special' => $specialFeeCalculator,
            'association' => $associationFeeCalculator,
            'storage' => $storageFeeCalculator
        ];
    }

    /**
     * @return array<string, FeeCalculatorInterface>
     */
    public function getAllCalculators(): array
    {
        return $this->calculators;
    }

    /**
     * @param string $type
     * @return FeeCalculatorInterface
     * @throws \InvalidArgumentException if calculator type is not found
     */
    public function getCalculator(string $type): FeeCalculatorInterface
    {
        if (!isset($this->calculators[$type])) {
            throw new \InvalidArgumentException("Unknown calculator type: $type");
        }
        return $this->calculators[$type];
    }
} 
 