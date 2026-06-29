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
        return AiMessage::CONVERSATION_ID;
    }

    public function messageUuid()
    {
        return AiMessage::MESSAGE_UUID;
    }

    public function role()
    {
        return AiMessage::ROLE;
    }

    public function content()
    {
        return AiMessage::CONTENT;
    }

    public function functionName()
    {
        return AiMessage::FUNCTION_NAME;
    }

    public function functionArguments()
    {
        return AiMessage::FUNCTION_ARGUMENTS;
    }

    public function functionResponse()
    {
        return AiMessage::FUNCTION_RESPONSE;
    }

    public function modelId()
    {
        return AiMessage::MODEL_ID;
    }

    public function inputTokens()
    {
        return AiMessage::INPUT_TOKENS;
    }

    public function outputTokens()
    {
        return AiMessage::OUTPUT_TOKENS;
    }

    public function totalTokens()
    {
        return AiMessage::TOTAL_TOKENS;
    }

    public function cost()
    {
        return AiMessage::COST;
    }

    public function responseTimeMs()
    {
        return AiMessage::RESPONSE_TIME_MS;
    }

    public function finishReason()
    {
        return AiMessage::FINISH_REASON;
    }

    public function metadata()
    {
        return AiMessage::METADATA;
    }

    public function rawRequest()
    {
        return AiMessage::RAW_REQUEST;
    }

    public function rawResponse()
    {
        return AiMessage::RAW_RESPONSE;
    }

    public function userRating()
    {
        return AiMessage::USER_RATING;
    }

    public function userFeedback()
    {
        return AiMessage::USER_FEEDBACK;
    }
    // functions
}