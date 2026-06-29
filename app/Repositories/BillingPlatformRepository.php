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
        return BillingPlatform::PLATFORM_NAME;
    }

    public function platformCode()
    {
        return BillingPlatform::PLATFORM_CODE;
    }

    public function description()
    {
        return BillingPlatform::DESCRIPTION;
    }

    public function isActive()
    {
        return BillingPlatform::IS_ACTIVE;
    }
    // functions
}