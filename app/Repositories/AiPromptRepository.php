<?php

namespace App\Repositories;

use App\Models\AiPrompt;

class AiPromptRepository extends BaseRepository
{
    public function __construct(AiPrompt $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function promptName()
    {
        return AiPrompt::PROMPT_NAME;
    }

    public function promptCode()
    {
        return AiPrompt::PROMPT_CODE;
    }

    public function category()
    {
        return AiPrompt::CATEGORY;
    }

    public function description()
    {
        return AiPrompt::DESCRIPTION;
    }

    public function systemPrompt()
    {
        return AiPrompt::SYSTEM_PROMPT;
    }

    public function userPromptTemplate()
    {
        return AiPrompt::USER_PROMPT_TEMPLATE;
    }

    public function examples()
    {
        return AiPrompt::EXAMPLES;
    }

    public function providerId()
    {
        return AiPrompt::PROVIDER_ID;
    }

    public function modelId()
    {
        return AiPrompt::MODEL_ID;
    }

    public function temperature()
    {
        return AiPrompt::TEMPERATURE;
    }

    public function maxTokens()
    {
        return AiPrompt::MAX_TOKENS;
    }

    public function responseFormat()
    {
        return AiPrompt::RESPONSE_FORMAT;
    }

    public function responseSchema()
    {
        return AiPrompt::RESPONSE_SCHEMA;
    }

    public function parseResponse()
    {
        return AiPrompt::PARSE_RESPONSE;
    }

    public function functions()
    {
        return AiPrompt::FUNCTIONS;
    }

    public function version()
    {
        return AiPrompt::VERSION;
    }

    public function isActive()
    {
        return AiPrompt::IS_ACTIVE;
    }

    public function createdBy()
    {
        return AiPrompt::CREATED_BY;
    }

    public function updatedBy()
    {
        return AiPrompt::UPDATED_BY;
    }
    // functions
}