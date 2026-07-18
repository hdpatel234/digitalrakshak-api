<?php

namespace App\Enums;

enum EmailServerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case FAILING = 'failing';
}
