<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case PENDING = "pending";
    case IN_PROGRESS = "in_progress";
    case VERIFIED = "verified";
    case SUCCESS = "success";
    case FAILED = "failed";
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
