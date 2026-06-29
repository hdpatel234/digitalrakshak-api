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
        return AiModel::PROVIDER_ID;
    }

    public function modelName()
    {
        return AiModel::MODEL_NAME;
    }

    public function modelCode()
    {
        return AiModel::MODEL_CODE;
    }

    public function modelType()
    {
        return AiModel::MODEL_TYPE;
    }

    public function description()
    {
        return AiModel::DESCRIPTION;
    }

    public function maxTokens()
    {
        return AiModel::MAX_TOKENS;
    }

    public function maxInputTokens()
    {
        return AiModel::MAX_INPUT_TOKENS;
    }

    public function maxOutputTokens()
    {
        return AiModel::MAX_OUTPUT_TOKENS;
    }

    public function supportsFunctions()
    {
        return AiModel::SUPPORTS_FUNCTIONS;
    }

    public function supportsVision()
    {
        return AiModel::SUPPORTS_VISION;
    }

    public function supportsStreaming()
    {
        return AiModel::SUPPORTS_STREAMING;
    }

    public function supportsJsonMode()
    {
        return AiModel::SUPPORTS_JSON_MODE;
    }

    public function inputCostPer1k()
    {
        return AiModel::INPUT_COST_PER_1K;
    }

    public function outputCostPer1k()
    {
        return AiModel::OUTPUT_COST_PER_1K;
    }

    public function currency()
    {
        return AiModel::CURRENCY;
    }

    public function isActive()
    {
        return AiModel::IS_ACTIVE;
    }

    public function isDefault()
    {
        return AiModel::IS_DEFAULT;
    }

    public function displayOrder()
    {
        return AiModel::DISPLAY_ORDER;
    }

    public function capabilities()
    {
        return AiModel::CAPABILITIES;
    }
    // functions
}