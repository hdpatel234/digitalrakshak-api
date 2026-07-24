<?php

namespace App\Enums;

enum UserConfigKey: string
{
    case LANGUAGE = 'language';
    case DATE_FORMAT = 'date_format';
    case TIME_FORMAT = 'time_format';
    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }
}
