<?php

namespace App\Enums;

enum ServiceType: string
{
    case AUTO = 'auto';
    case MANUAL = 'manual';
    public static function values(): array
    {
        return array_map(static fn(self $type): string => $type->value, self::cases());
    }
}
