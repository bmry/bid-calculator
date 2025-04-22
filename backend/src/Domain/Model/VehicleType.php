<?php

namespace Progi\Domain\Model;

enum VehicleType: string
{
    case Common = 'common';
    case Luxury = 'luxury';

    public static function getValidTypes(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }
}

