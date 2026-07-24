<?php

namespace App\Enums;

enum SocialLoginProvider: string
{
    case DIGILOCKER = 'digilocker';
    case FACEBOOK = 'facebook';
    case TWITTER = 'twitter';
    case GOOGLE = 'google';
    case LINKEDIN = 'linkedin';
    case APPLE = 'apple';
    public static function values(): array
    {
        return array_map(static fn(self $event): string => $event->value, self::cases());
    }
}
