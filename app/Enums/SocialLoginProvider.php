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
}
