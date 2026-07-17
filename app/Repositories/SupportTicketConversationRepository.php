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
        return SupportTicketConversation::TICKET_ID;
    }

    public function externalConversationId()
    {
        return SupportTicketConversation::EXTERNAL_CONVERSATION_ID;
    }

    public function message()
    {
        return SupportTicketConversation::MESSAGE;
    }

    public function senderType()
    {
        return SupportTicketConversation::SENDER_TYPE;
    }

    public function senderName()
    {
        return SupportTicketConversation::SENDER_NAME;
    }

    public function senderEmail()
    {
        return SupportTicketConversation::SENDER_EMAIL;
    }

    public function isInternal()
    {
        return SupportTicketConversation::IS_INTERNAL;
    }

    public function attachments()
    {
        return SupportTicketConversation::ATTACHMENTS;
    }

    public function conversationData()
    {
        return SupportTicketConversation::CONVERSATION_DATA;
    }

    public function syncStatus()
    {
        return SupportTicketConversation::SYNC_STATUS;
    }
    // functions
}
