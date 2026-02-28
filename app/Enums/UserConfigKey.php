<?php

namespace App\Enums;

enum UserConfigKey: string
{
    case LANGUAGE = 'language';
    case DATE_FORMAT = 'date_format';
    case TIME_FORMAT = 'time_format';
}
