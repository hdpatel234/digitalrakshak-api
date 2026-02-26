<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TicketConversation extends BaseModel
{
    
    protected $table = "ticket_conversations";
    
    const TICKET_ID = "ticket_id";
    const EXTERNAL_CONVERSATION_ID = "external_conversation_id";
    const MESSAGE = "message";
    const SENDER_TYPE = "sender_type";
    const SENDER_NAME = "sender_name";
    const SENDER_EMAIL = "sender_email";
    const IS_INTERNAL = "is_internal";
    const ATTACHMENTS = "attachments";
    const CONVERSATION_DATA = "conversation_data";
    const SYNC_STATUS = "sync_status";
    protected $fillable = [
        self::TICKET_ID,
        self::EXTERNAL_CONVERSATION_ID,
        self::MESSAGE,
        self::SENDER_TYPE,
        self::SENDER_NAME,
        self::SENDER_EMAIL,
        self::IS_INTERNAL,
        self::ATTACHMENTS,
        self::CONVERSATION_DATA,
        self::SYNC_STATUS,
    ];
}