<?php

namespace App\Repositories;

use App\Models\SupportConfig;

class SupportConfigRepository extends BaseRepository
{
    public function __construct(SupportConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function supportPlatformId()
    {
        return $this->model::SUPPORT_PLATFORM_ID;
    }

    public function configName()
    {
        return $this->model::CONFIG_NAME;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function apiUrl()
    {
        return $this->model::API_URL;
    }

    public function apiKey()
    {
        return $this->model::API_KEY;
    }

    public function apiSecret()
    {
        return $this->model::API_SECRET;
    }

    public function apiToken()
    {
        return $this->model::API_TOKEN;
    }

    public function webhookSecret()
    {
        return $this->model::WEBHOOK_SECRET;
    }

    public function additionalConfig()
    {
        return $this->model::ADDITIONAL_CONFIG;
    }

    public function defaultPriority()
    {
        return $this->model::DEFAULT_PRIORITY;
    }

    public function defaultDepartment()
    {
        return $this->model::DEFAULT_DEPARTMENT;
    }

    public function defaultAssignee()
    {
        return $this->model::DEFAULT_ASSIGNEE;
    }

    public function status()
    {
        return $this->model::STATUS;
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