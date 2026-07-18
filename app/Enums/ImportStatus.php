<?php

namespace App\Enums;

enum ImportStatus: string
{
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_map(static fn (self $status): string => $status->value, self::cases());
    }
}
