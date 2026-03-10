<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case CREATED = 'created';
    case INVITED = 'invited';
    case ACTIVE = 'active';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $status): string => $status->value, self::cases());
    }
}

