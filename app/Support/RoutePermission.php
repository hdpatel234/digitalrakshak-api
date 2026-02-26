<?php

namespace App\Support;

use Illuminate\Support\Str;

class RoutePermission
{
    public static function fromUriAndMethod(string $uri, string $method): string
    {
        $normalizedUri = self::normalizeUri($uri);
        $normalizedMethod = Str::lower($method);

        return "{$normalizedMethod}.{$normalizedUri}";
    }

    public static function normalizeUri(string $uri): string
    {
        return Str::of($uri)
            ->trim('/')
            ->when(
                fn ($value) => $value->startsWith('api/'),
                fn ($value) => $value->replaceFirst('api/', '')
            )
            ->replace(['{', '}'], '')
            ->replace('/', '.')
            ->replace('-', '_')
            ->lower()
            ->value();
    }
}
