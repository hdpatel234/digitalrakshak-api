<?php

namespace App\Enums;

enum ServiceFieldName: string
{
    case BENEFICIARY_ACCOUNT = 'beneficiary_account';
    case BENEFICIARY_IFSC = 'beneficiary_ifsc';
    case UAN = 'uan';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
