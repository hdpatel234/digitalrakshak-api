<?php

namespace App\Repositories;

use App\Models\SupportPlatform;

class SupportPlatformRepository extends BaseRepository
{
    public function __construct(SupportPlatform $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function platformName()
    {
        return $this->model::PLATFORM_NAME;
    }

    public function platformCode()
    {
        return $this->model::PLATFORM_CODE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
}