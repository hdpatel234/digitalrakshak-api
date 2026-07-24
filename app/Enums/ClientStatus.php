<?php

namespace App\Enums;

enum ClientStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        };
    }
}
