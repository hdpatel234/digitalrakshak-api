<?php

namespace App\Repositories;

use App\Models\ServiceProvider;

class ServiceProviderRepository extends BaseRepository
{
    public function __construct(ServiceProvider $model)
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

    public function supportEmail()
    {
        return $this->model::SUPPORT_EMAIL;
    }

    public function supportPhone()
    {
        return $this->model::SUPPORT_PHONE;
    }

    public function documentationUrl()
    {
        return $this->model::DOCUMENTATION_URL;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function priority()
    {
        return $this->model::PRIORITY;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}