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
        return $this->model::CONFIG_NAME;
    }

    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function modelId()
    {
        return $this->model::MODEL_ID;
    }

    public function apiKey()
    {
        return $this->model::API_KEY;
    }

    public function apiSecret()
    {
        return $this->model::API_SECRET;
    }

    public function organizationId()
    {
        return $this->model::ORGANIZATION_ID;
    }

    public function projectId()
    {
        return $this->model::PROJECT_ID;
    }

    public function baseUrl()
    {
        return $this->model::BASE_URL;
    }

    public function defaultModel()
    {
        return $this->model::DEFAULT_MODEL;
    }

    public function defaultTemperature()
    {
        return $this->model::DEFAULT_TEMPERATURE;
    }

    public function defaultMaxTokens()
    {
        return $this->model::DEFAULT_MAX_TOKENS;
    }

    public function defaultTopP()
    {
        return $this->model::DEFAULT_TOP_P;
    }

    public function defaultFrequencyPenalty()
    {
        return $this->model::DEFAULT_FREQUENCY_PENALTY;
    }

    public function defaultPresencePenalty()
    {
        return $this->model::DEFAULT_PRESENCE_PENALTY;
    }

    public function requestsPerMinute()
    {
        return $this->model::REQUESTS_PER_MINUTE;
    }

    public function tokensPerMinute()
    {
        return $this->model::TOKENS_PER_MINUTE;
    }

    public function enableStreaming()
    {
        return $this->model::ENABLE_STREAMING;
    }

    public function enableFunctions()
    {
        return $this->model::ENABLE_FUNCTIONS;
    }

    public function enableVision()
    {
        return $this->model::ENABLE_VISION;
    }

    public function environment()
    {
        return $this->model::ENVIRONMENT;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function totalRequests()
    {
        return $this->model::TOTAL_REQUESTS;
    }

    public function totalTokens()
    {
        return $this->model::TOTAL_TOKENS;
    }

    public function totalCost()
    {
        return $this->model::TOTAL_COST;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
    }

    public function healthStatus()
    {
        return $this->model::HEALTH_STATUS;
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