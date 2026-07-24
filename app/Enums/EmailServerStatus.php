<?php

namespace App\Enums;

enum EmailServerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case FAILING = 'failing';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
