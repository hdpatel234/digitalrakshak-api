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
        return $this->model::CONVERSATION_UUID;
    }

    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function configId()
    {
        return $this->model::CONFIG_ID;
    }

    public function modelId()
    {
        return $this->model::MODEL_ID;
    }

    public function promptId()
    {
        return $this->model::PROMPT_ID;
    }

    public function title()
    {
        return $this->model::TITLE;
    }

    public function context()
    {
        return $this->model::CONTEXT;
    }

    public function totalTokens()
    {
        return $this->model::TOTAL_TOKENS;
    }

    public function totalCost()
    {
        return $this->model::TOTAL_COST;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function lastMessageAt()
    {
        return $this->model::LAST_MESSAGE_AT;
    }
    // functions
}