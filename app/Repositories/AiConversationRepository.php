<?php

namespace App\Repositories;

use App\Models\AiConversation;

class AiConversationRepository extends BaseRepository
{
    public function __construct(AiConversation $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function conversationUuid()
    {
        return AiConversation::CONVERSATION_UUID;
    }

    public function userId()
    {
        return AiConversation::USER_ID;
    }

    public function clientId()
    {
        return AiConversation::CLIENT_ID;
    }

    public function configId()
    {
        return AiConversation::CONFIG_ID;
    }

    public function modelId()
    {
        return AiConversation::MODEL_ID;
    }

    public function promptId()
    {
        return AiConversation::PROMPT_ID;
    }

    public function title()
    {
        return AiConversation::TITLE;
    }

    public function context()
    {
        return AiConversation::CONTEXT;
    }

    public function totalTokens()
    {
        return AiConversation::TOTAL_TOKENS;
    }

    public function totalCost()
    {
        return AiConversation::TOTAL_COST;
    }

    public function status()
    {
        return AiConversation::STATUS;
    }

    public function lastMessageAt()
    {
        return AiConversation::LAST_MESSAGE_AT;
    }
    // functions
}