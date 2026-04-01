<?php

namespace App\Repositories;

use App\Models\AiUsageLog;

class AiUsageLogRepository extends BaseRepository
{
    public function __construct(AiUsageLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function configId()
    {
        return $this->model::CONFIG_ID;
    }

    public function modelId()
    {
        return $this->model::MODEL_ID;
    }

    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function conversationId()
    {
        return $this->model::CONVERSATION_ID;
    }

    public function messageId()
    {
        return $this->model::MESSAGE_ID;
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

    public function requestType()
    {
        return $this->model::REQUEST_TYPE;
    }

    public function responseTimeMs()
    {
        return $this->model::RESPONSE_TIME_MS;
    }

    public function success()
    {
        return $this->model::SUCCESS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }
    // functions
}