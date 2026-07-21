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
        return ServiceProvider::PROVIDER_NAME;
    }

    public function providerCode()
    {
        return ServiceProvider::PROVIDER_CODE;
    }

    public function providerType()
    {
        return ServiceProvider::PROVIDER_TYPE;
    }

    public function description()
    {
        return ServiceProvider::DESCRIPTION;
    }

    public function website()
    {
        return ServiceProvider::WEBSITE;
    }

    public function supportEmail()
    {
        return ServiceProvider::SUPPORT_EMAIL;
    }

    public function supportPhone()
    {
        return ServiceProvider::SUPPORT_PHONE;
    }

    public function documentationUrl()
    {
        return ServiceProvider::DOCUMENTATION_URL;
    }

    public function status()
    {
        return ServiceProvider::STATUS;
    }

    public function isDefault()
    {
        return ServiceProvider::IS_DEFAULT;
    }

    public function priority()
    {
        return ServiceProvider::PRIORITY;
    }

    public function createdBy()
    {
        return ServiceProvider::CREATED_BY;
    }

    public function updatedBy()
    {
        return ServiceProvider::UPDATED_BY;
    }
    // functions
}
