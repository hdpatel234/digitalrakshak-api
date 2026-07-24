<?php

namespace App\Enums;

enum VerificationMethod: string
{
    case AUTO = "auto";
    case MANUAL = "manual";
    case HYBRID = "hybrid";
    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }
    public static function labels(): array
    {
        return array_map(static fn(self $status): string => $status->label(), self::cases());
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
