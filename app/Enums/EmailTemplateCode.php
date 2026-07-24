<?php

namespace App\Enums;

enum EmailTemplateCode: string
{
    case CANDIDATE_INVITATION_FORM = 'EMAIL-CLIENT-INV-001';
    case CLIENT_ORDER_CONFIRMATION = 'EMAIL-CLIENT-ORD-CONF-001';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
