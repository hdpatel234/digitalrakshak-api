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
        return $this->model::NAME;
    }

    public function code()
    {
        return $this->model::CODE;
    }

    public function class()
    {
        return $this->model::CLASS;
    }

    public function configFields()
    {
        return $this->model::CONFIG_FIELDS;
    }

    public function credentials()
    {
        return $this->model::CREDENTIALS;
    }

    public function settings()
    {
        return $this->model::SETTINGS;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function priority()
    {
        return $this->model::PRIORITY;
    }
    // functions
}