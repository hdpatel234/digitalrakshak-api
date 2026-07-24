<?php

namespace App\Enums;

enum EmailPriority: string
{
    case CRITICAL = 'critical';
    case HIGH = 'high';
    case NORMAL = 'normal';
    case LOW = 'low';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
