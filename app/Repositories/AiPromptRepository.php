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
        return $this->model::PROMPT_NAME;
    }

    public function promptCode()
    {
        return $this->model::PROMPT_CODE;
    }

    public function category()
    {
        return $this->model::CATEGORY;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function systemPrompt()
    {
        return $this->model::SYSTEM_PROMPT;
    }

    public function userPromptTemplate()
    {
        return $this->model::USER_PROMPT_TEMPLATE;
    }

    public function examples()
    {
        return $this->model::EXAMPLES;
    }

    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function modelId()
    {
        return $this->model::MODEL_ID;
    }

    public function temperature()
    {
        return $this->model::TEMPERATURE;
    }

    public function maxTokens()
    {
        return $this->model::MAX_TOKENS;
    }

    public function responseFormat()
    {
        return $this->model::RESPONSE_FORMAT;
    }

    public function responseSchema()
    {
        return $this->model::RESPONSE_SCHEMA;
    }

    public function parseResponse()
    {
        return $this->model::PARSE_RESPONSE;
    }

    public function functions()
    {
        return $this->model::FUNCTIONS;
    }

    public function version()
    {
        return $this->model::VERSION;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
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