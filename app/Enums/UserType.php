<?php

namespace App\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'super_admin';
    case CLIENT_ADMIN = 'client_admin';
    case CLIENT_USER = 'client_user';
    case ADMIN_USER = 'admin_user';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type): string => $type->value, self::cases());
    }
}
