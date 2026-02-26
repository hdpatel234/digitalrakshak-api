<?php

namespace App\Repositories;

use App\Models\BillingPlatform;

class BillingPlatformRepository extends BaseRepository
{
    public function __construct(BillingPlatform $model)
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