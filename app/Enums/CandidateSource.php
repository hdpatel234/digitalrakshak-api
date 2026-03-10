<?php

namespace App\Enums;

enum CandidateSource: string
{
    case CREATE_FORM = 'create_form';
    case IMPORT_FILE = 'import_file';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $event): string => $event->value, self::cases());
    }
}

