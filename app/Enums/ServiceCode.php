<?php

namespace App\Enums;

enum ServiceCode: string
{
    case EMP_VER = 'EMP_VER';
    case ID_VERIFY_PAN = 'ID_VERIFY_PAN';
    case ID_VERIFY_AADHAR = 'ID_VERIFY_AADHAR';
    case COURT_VERIFY = 'COURT_VERIFY';
    case ADDRESS_VERIFY_PHYSICAL = 'ADDRESS_VERIFY_PHYSICAL';
    case ADDRESS_VERIFY_DIGITAL = 'ADDRESS_VERIFY_DIGITAL';
    case EDU_VERIFY = 'EDU_VERIFY';

    public static function values(): array
    {
        return array_map(static fn (self $status): string => $status->value, self::cases());
    }
}
