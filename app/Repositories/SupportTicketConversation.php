<?php

namespace App\Repositories;

use App\Models\SupportTicketConversation;

class SupportTicketConversationRepository extends BaseRepository
{
    public function __construct(SupportTicketConversation $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function ticketId()
    {
        return $this->model::TICKET_ID;
    }

    public function externalConversationId()
    {
        return $this->model::EXTERNAL_CONVERSATION_ID;
    }

    public function message()
    {
        return $this->model::MESSAGE;
    }

    public function senderType()
    {
        return $this->model::SENDER_TYPE;
    }

    public function senderName()
    {
        return $this->model::SENDER_NAME;
    }

    public function senderEmail()
    {
        return $this->model::SENDER_EMAIL;
    }

    public function isInternal()
    {
        return $this->model::IS_INTERNAL;
    }

    public function attachments()
    {
        return $this->model::ATTACHMENTS;
    }

    public function conversationData()
    {
        return $this->model::CONVERSATION_DATA;
    }

    public function syncStatus()
    {
        return $this->model::SYNC_STATUS;
    }
    // functions
}
