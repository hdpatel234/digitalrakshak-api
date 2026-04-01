<?php

namespace App\Repositories;

use App\Models\AiModel;

class AiModelRepository extends BaseRepository
{
    public function __construct(AiModel $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function modelName()
    {
        return $this->model::MODEL_NAME;
    }

    public function modelCode()
    {
        return $this->model::MODEL_CODE;
    }

    public function modelType()
    {
        return $this->model::MODEL_TYPE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function maxTokens()
    {
        return $this->model::MAX_TOKENS;
    }

    public function maxInputTokens()
    {
        return $this->model::MAX_INPUT_TOKENS;
    }

    public function maxOutputTokens()
    {
        return $this->model::MAX_OUTPUT_TOKENS;
    }

    public function supportsFunctions()
    {
        return $this->model::SUPPORTS_FUNCTIONS;
    }

    public function supportsVision()
    {
        return $this->model::SUPPORTS_VISION;
    }

    public function supportsStreaming()
    {
        return $this->model::SUPPORTS_STREAMING;
    }

    public function supportsJsonMode()
    {
        return $this->model::SUPPORTS_JSON_MODE;
    }

    public function inputCostPer1k()
    {
        return $this->model::INPUT_COST_PER_1K;
    }

    public function outputCostPer1k()
    {
        return $this->model::OUTPUT_COST_PER_1K;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function capabilities()
    {
        return $this->model::CAPABILITIES;
    }
    // functions
}