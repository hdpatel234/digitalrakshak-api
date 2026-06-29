<?php

namespace App\Repositories;

use App\Models\AiProvider;

class AiProviderRepository extends BaseRepository
{
    public function __construct(AiProvider $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerName()
    {
        return AiProvider::PROVIDER_NAME;
    }

    public function providerCode()
    {
        return AiProvider::PROVIDER_CODE;
    }

    public function providerType()
    {
        return AiProvider::PROVIDER_TYPE;
    }

    public function description()
    {
        return AiProvider::DESCRIPTION;
    }

    public function website()
    {
        return AiProvider::WEBSITE;
    }

    public function documentationUrl()
    {
        return AiProvider::DOCUMENTATION_URL;
    }

    public function icon()
    {
        return AiProvider::ICON;
    }

    public function isActive()
    {
        return AiProvider::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return AiProvider::DISPLAY_ORDER;
    }
    // functions
}