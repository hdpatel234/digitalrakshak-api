<?php

namespace App\Enums;

enum CandidateEvent: string
{
    case CREATED = 'created';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $event): string => $event->value, self::cases());
    }
}

