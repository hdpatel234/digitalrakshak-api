<?php

namespace App\Repositories;

use App\Models\DocumentPlatform;

class DocumentPlatformRepository extends BaseRepository
{
    public function __construct(DocumentPlatform $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function platformName()
    {
        return DocumentPlatform::PLATFORM_NAME;
    }

    public function platformCode()
    {
        return DocumentPlatform::PLATFORM_CODE;
    }

    public function description()
    {
        return DocumentPlatform::DESCRIPTION;
    }

    public function isActive()
    {
        return DocumentPlatform::IS_ACTIVE;
    }
    // functions
}