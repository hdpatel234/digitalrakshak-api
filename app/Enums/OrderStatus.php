<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
