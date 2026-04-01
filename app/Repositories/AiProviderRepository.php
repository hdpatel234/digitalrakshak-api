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
        return $this->model::PROVIDER_NAME;
    }

    public function providerCode()
    {
        return $this->model::PROVIDER_CODE;
    }

    public function providerType()
    {
        return $this->model::PROVIDER_TYPE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function website()
    {
        return $this->model::WEBSITE;
    }

    public function documentationUrl()
    {
        return $this->model::DOCUMENTATION_URL;
    }

    public function icon()
    {
        return $this->model::ICON;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }
    // functions
}