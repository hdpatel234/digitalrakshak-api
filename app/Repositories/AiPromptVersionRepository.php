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
        return AiPromptVersion::PROMPT_ID;
    }

    public function version()
    {
        return AiPromptVersion::VERSION;
    }

    public function systemPrompt()
    {
        return AiPromptVersion::SYSTEM_PROMPT;
    }

    public function userPromptTemplate()
    {
        return AiPromptVersion::USER_PROMPT_TEMPLATE;
    }

    public function examples()
    {
        return AiPromptVersion::EXAMPLES;
    }

    public function temperature()
    {
        return AiPromptVersion::TEMPERATURE;
    }

    public function maxTokens()
    {
        return AiPromptVersion::MAX_TOKENS;
    }

    public function createdBy()
    {
        return AiPromptVersion::CREATED_BY;
    }
    // functions
}