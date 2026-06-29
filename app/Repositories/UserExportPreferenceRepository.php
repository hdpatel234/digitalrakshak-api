<?php

namespace App\Repositories;

use App\Models\UserExportPreference;

class UserExportPreferenceRepository extends BaseRepository
{
    public function __construct(UserExportPreference $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserExportPreference::USER_ID;
    }

    public function defaultFormat()
    {
        return UserExportPreference::DEFAULT_FORMAT;
    }

    public function paperSize()
    {
        return UserExportPreference::PAPER_SIZE;
    }

    public function orientation()
    {
        return UserExportPreference::ORIENTATION;
    }

    public function includeTimestamps()
    {
        return UserExportPreference::INCLUDE_TIMESTAMPS;
    }

    public function includeMetadata()
    {
        return UserExportPreference::INCLUDE_METADATA;
    }

    public function compression()
    {
        return UserExportPreference::COMPRESSION;
    }

    public function emailOnComplete()
    {
        return UserExportPreference::EMAIL_ON_COMPLETE;
    }
    // functions
}