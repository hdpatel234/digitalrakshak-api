<?php

namespace App\Repositories;

use App\Models\ApiProvider;

class ApiProviderRepository extends BaseRepository
{
    public function __construct(ApiProvider $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function name()
    {
        return ApiProvider::NAME;
    }

    public function code()
    {
        return ApiProvider::CODE;
    }

    public function class()
    {
        return ApiProvider::PROVIDER_CLASS;
    }

    public function configFields()
    {
        return ApiProvider::CONFIG_FIELDS;
    }

    public function credentials()
    {
        return ApiProvider::CREDENTIALS;
    }

    public function settings()
    {
        return ApiProvider::SETTINGS;
    }

    public function isActive()
    {
        return ApiProvider::IS_ACTIVE;
    }

    public function isDefault()
    {
        return ApiProvider::IS_DEFAULT;
    }

    public function priority()
    {
        return ApiProvider::PRIORITY;
    }
    // functions
}