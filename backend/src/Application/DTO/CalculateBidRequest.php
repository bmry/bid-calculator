<?php
declare(strict_types=1);

namespace Progi\Application\DTO;

use Progi\Domain\Model\VehicleType;
use Symfony\Component\Validator\Constraints as Assert;

final class CalculateBidRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\Positive]
    public float $price;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['common', 'luxury'])]
    public string $vehicleType;

    public function __construct(float $price, string $vehicleType)
    {
        $this->price = $price;
        $this->vehicleType = $vehicleType;
    }
}
