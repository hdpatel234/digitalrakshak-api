<?php

namespace App\Repositories;

use App\Models\AiApiConfig;

class AiApiConfigRepository extends BaseRepository
{
    public function __construct(AiApiConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function configName()
    {
        return AiApiConfig::CONFIG_NAME;
    }

    public function providerId()
    {
        return AiApiConfig::PROVIDER_ID;
    }

    public function modelId()
    {
        return AiApiConfig::MODEL_ID;
    }

    public function apiKey()
    {
        return AiApiConfig::API_KEY;
    }

    public function apiSecret()
    {
        return AiApiConfig::API_SECRET;
    }

    public function organizationId()
    {
        return AiApiConfig::ORGANIZATION_ID;
    }

    public function projectId()
    {
        return AiApiConfig::PROJECT_ID;
    }

    public function baseUrl()
    {
        return AiApiConfig::BASE_URL;
    }

    public function defaultModel()
    {
        return AiApiConfig::DEFAULT_MODEL;
    }

    public function defaultTemperature()
    {
        return AiApiConfig::DEFAULT_TEMPERATURE;
    }

    public function defaultMaxTokens()
    {
        return AiApiConfig::DEFAULT_MAX_TOKENS;
    }

    public function defaultTopP()
    {
        return AiApiConfig::DEFAULT_TOP_P;
    }

    public function defaultFrequencyPenalty()
    {
        return AiApiConfig::DEFAULT_FREQUENCY_PENALTY;
    }

    public function defaultPresencePenalty()
    {
        return AiApiConfig::DEFAULT_PRESENCE_PENALTY;
    }

    public function requestsPerMinute()
    {
        return AiApiConfig::REQUESTS_PER_MINUTE;
    }

    public function tokensPerMinute()
    {
        return AiApiConfig::TOKENS_PER_MINUTE;
    }

    public function enableStreaming()
    {
        return AiApiConfig::ENABLE_STREAMING;
    }

    public function enableFunctions()
    {
        return AiApiConfig::ENABLE_FUNCTIONS;
    }

    public function enableVision()
    {
        return AiApiConfig::ENABLE_VISION;
    }

    public function environment()
    {
        return AiApiConfig::ENVIRONMENT;
    }

    public function isActive()
    {
        return AiApiConfig::IS_ACTIVE;
    }

    public function isDefault()
    {
        return AiApiConfig::IS_DEFAULT;
    }

    public function totalRequests()
    {
        return AiApiConfig::TOTAL_REQUESTS;
    }

    public function totalTokens()
    {
        return AiApiConfig::TOTAL_TOKENS;
    }

    public function totalCost()
    {
        return AiApiConfig::TOTAL_COST;
    }

    public function lastUsedAt()
    {
        return AiApiConfig::LAST_USED_AT;
    }

    public function healthStatus()
    {
        return AiApiConfig::HEALTH_STATUS;
    }

    public function createdBy()
    {
        return AiApiConfig::CREATED_BY;
    }

    public function updatedBy()
    {
        return AiApiConfig::UPDATED_BY;
    }
    // functions
}