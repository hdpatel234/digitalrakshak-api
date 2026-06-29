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
        return SupportPlatform::PLATFORM_NAME;
    }

    public function platformCode()
    {
        return SupportPlatform::PLATFORM_CODE;
    }

    public function description()
    {
        return SupportPlatform::DESCRIPTION;
    }

    public function isActive()
    {
        return SupportPlatform::IS_ACTIVE;
    }
    // functions
}