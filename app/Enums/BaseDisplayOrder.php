<?php

namespace App\Enums;

enum BaseDisplayOrder: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }
}
