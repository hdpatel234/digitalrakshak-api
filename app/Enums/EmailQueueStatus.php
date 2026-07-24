<?php

namespace App\Enums;

enum EmailQueueStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SENT = 'sent';
    case FAILED = 'failed';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
