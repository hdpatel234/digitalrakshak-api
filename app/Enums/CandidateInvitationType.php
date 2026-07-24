<?php

namespace App\Enums;

enum CandidateInvitationType: string
{
    case EMAIL = 'email';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
