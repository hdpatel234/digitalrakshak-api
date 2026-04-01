<?php

namespace App\Repositories;

use App\Models\AiPromptVersion;

class AiPromptVersionRepository extends BaseRepository
{
    public function __construct(AiPromptVersion $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function promptId()
    {
        return $this->model::PROMPT_ID;
    }

    public function version()
    {
        return $this->model::VERSION;
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

    public function temperature()
    {
        return $this->model::TEMPERATURE;
    }

    public function maxTokens()
    {
        return $this->model::MAX_TOKENS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }
    // functions
}