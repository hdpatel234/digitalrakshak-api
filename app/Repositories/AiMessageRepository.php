<?php

namespace App\Repositories;

use App\Models\AiMessage;

class AiMessageRepository extends BaseRepository
{
    public function __construct(AiMessage $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function conversationId()
    {
        return $this->model::CONVERSATION_ID;
    }

    public function messageUuid()
    {
        return $this->model::MESSAGE_UUID;
    }

    public function role()
    {
        return $this->model::ROLE;
    }

    public function content()
    {
        return $this->model::CONTENT;
    }

    public function functionName()
    {
        return $this->model::FUNCTION_NAME;
    }

    public function functionArguments()
    {
        return $this->model::FUNCTION_ARGUMENTS;
    }

    public function functionResponse()
    {
        return $this->model::FUNCTION_RESPONSE;
    }

    public function modelId()
    {
        return $this->model::MODEL_ID;
    }

    public function inputTokens()
    {
        return $this->model::INPUT_TOKENS;
    }

    public function outputTokens()
    {
        return $this->model::OUTPUT_TOKENS;
    }

    public function totalTokens()
    {
        return $this->model::TOTAL_TOKENS;
    }

    public function cost()
    {
        return $this->model::COST;
    }

    public function responseTimeMs()
    {
        return $this->model::RESPONSE_TIME_MS;
    }

    public function finishReason()
    {
        return $this->model::FINISH_REASON;
    }

    public function metadata()
    {
        return $this->model::METADATA;
    }

    public function rawRequest()
    {
        return $this->model::RAW_REQUEST;
    }

    public function rawResponse()
    {
        return $this->model::RAW_RESPONSE;
    }

    public function userRating()
    {
        return $this->model::USER_RATING;
    }

    public function userFeedback()
    {
        return $this->model::USER_FEEDBACK;
    }
    // functions
}