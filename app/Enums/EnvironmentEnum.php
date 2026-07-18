<?php

namespace App\Enums;

enum EnvironmentEnum: string
{
    case PRODUCTION = 'production';
    case SANDBOX = 'sandbox';
    case TESTING = 'testing';
    case DEVELOPMENT = 'development';

    public static function values(): array
    {
        return array_map(static fn (self $env): string => $env->value, self::cases());
    }
}
