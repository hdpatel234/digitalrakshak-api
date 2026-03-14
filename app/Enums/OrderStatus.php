<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
