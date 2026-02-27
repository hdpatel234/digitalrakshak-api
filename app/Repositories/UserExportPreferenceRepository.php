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
        return $this->model::USER_ID;
    }

    public function defaultFormat()
    {
        return $this->model::DEFAULT_FORMAT;
    }

    public function paperSize()
    {
        return $this->model::PAPER_SIZE;
    }

    public function orientation()
    {
        return $this->model::ORIENTATION;
    }

    public function includeTimestamps()
    {
        return $this->model::INCLUDE_TIMESTAMPS;
    }

    public function includeMetadata()
    {
        return $this->model::INCLUDE_METADATA;
    }

    public function compression()
    {
        return $this->model::COMPRESSION;
    }

    public function emailOnComplete()
    {
        return $this->model::EMAIL_ON_COMPLETE;
    }
    // functions
}