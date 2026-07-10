<?php

namespace App\Enums;

enum TransactionType: string
{
    case SUBSCRIPTIONS = 'subscriptions';
    case ONE_TIME = 'one_time';
    case REFUNDS = 'refunds';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIPTIONS => 'Subscriptions',
            self::ONE_TIME => 'One-time Payments',
            self::REFUNDS => 'Refunds',
        };
    }
}
