<?php

namespace App\Enums;

enum EmailPriority: string
{
    case CRITICAL = 'critical';
    case HIGH = 'high';
    case NORMAL = 'normal';
    case LOW = 'low';
}
