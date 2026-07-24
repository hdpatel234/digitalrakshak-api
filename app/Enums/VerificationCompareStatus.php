<?php

namespace App\Enums;

enum VerificationCompareStatus: string
{
    case MATCH = "match";
    case MISMATCH = "mismatch";
    case PARTIAL_MATCH = "partial_match";
    case NOT_APPLICABLE = "not_applicable";
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
