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
        return AiUsageLog::CONFIG_ID;
    }

    public function modelId()
    {
        return AiUsageLog::MODEL_ID;
    }

    public function userId()
    {
        return AiUsageLog::USER_ID;
    }

    public function clientId()
    {
        return AiUsageLog::CLIENT_ID;
    }

    public function conversationId()
    {
        return AiUsageLog::CONVERSATION_ID;
    }

    public function messageId()
    {
        return AiUsageLog::MESSAGE_ID;
    }

    public function inputTokens()
    {
        return AiUsageLog::INPUT_TOKENS;
    }

    public function outputTokens()
    {
        return AiUsageLog::OUTPUT_TOKENS;
    }

    public function totalTokens()
    {
        return AiUsageLog::TOTAL_TOKENS;
    }

    public function cost()
    {
        return AiUsageLog::COST;
    }

    public function requestType()
    {
        return AiUsageLog::REQUEST_TYPE;
    }

    public function responseTimeMs()
    {
        return AiUsageLog::RESPONSE_TIME_MS;
    }

    public function success()
    {
        return AiUsageLog::SUCCESS;
    }

    public function errorMessage()
    {
        return AiUsageLog::ERROR_MESSAGE;
    }
    // functions
}