<?php

namespace App\Enums;

enum CandidateInvitationStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
