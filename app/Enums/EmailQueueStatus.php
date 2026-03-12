<?php

namespace App\Enums;

enum EmailQueueStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SENT = 'sent';
    case FAILED = 'failed';
}
