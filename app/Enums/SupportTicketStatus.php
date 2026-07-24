<?php

namespace App\Enums;

enum SupportTicketStatus: string
{
    case OPEN = 'open';
    case PENDING = 'pending';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    public function name(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::PENDING => 'Pending',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }
    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }
}
