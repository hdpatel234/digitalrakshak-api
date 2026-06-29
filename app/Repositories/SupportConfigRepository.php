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
        return SupportConfig::SUPPORT_PLATFORM_ID;
    }

    public function configName()
    {
        return SupportConfig::CONFIG_NAME;
    }

    public function isDefault()
    {
        return SupportConfig::IS_DEFAULT;
    }

    public function apiUrl()
    {
        return SupportConfig::API_URL;
    }

    public function apiKey()
    {
        return SupportConfig::API_KEY;
    }

    public function apiSecret()
    {
        return SupportConfig::API_SECRET;
    }

    public function apiToken()
    {
        return SupportConfig::API_TOKEN;
    }

    public function webhookSecret()
    {
        return SupportConfig::WEBHOOK_SECRET;
    }

    public function additionalConfig()
    {
        return SupportConfig::ADDITIONAL_CONFIG;
    }

    public function defaultPriority()
    {
        return SupportConfig::DEFAULT_PRIORITY;
    }

    public function defaultDepartment()
    {
        return SupportConfig::DEFAULT_DEPARTMENT;
    }

    public function defaultAssignee()
    {
        return SupportConfig::DEFAULT_ASSIGNEE;
    }

    public function status()
    {
        return SupportConfig::STATUS;
    }

    public function createdBy()
    {
        return SupportConfig::CREATED_BY;
    }

    public function updatedBy()
    {
        return SupportConfig::UPDATED_BY;
    }
    // functions
}