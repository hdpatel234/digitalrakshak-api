<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailLog extends BaseModel
{
    
    protected $table = "email_logs";
    
    const EMAIL_QUEUE_ID = "email_queue_id";
    const EMAIL_UID = "email_uid";
    const TO_EMAIL = "to_email";
    const SUBJECT = "subject";
    const SERVER_ID = "server_id";
    const MESSAGE_ID = "message_id";
    const STATUS = "status";
    const PROVIDER_RESPONSE = "provider_response";
    const ERROR_MESSAGE = "error_message";
    const OPENS = "opens";
    const CLICKS = "clicks";
    const SENT_AT = "sent_at";
    const OPENED_AT = "opened_at";
    const CLICKED_AT = "clicked_at";
    const METADATA = "metadata";
    protected $fillable = [
        self::EMAIL_QUEUE_ID,
        self::EMAIL_UID,
        self::TO_EMAIL,
        self::SUBJECT,
        self::SERVER_ID,
        self::MESSAGE_ID,
        self::STATUS,
        self::PROVIDER_RESPONSE,
        self::ERROR_MESSAGE,
        self::OPENS,
        self::CLICKS,
        self::SENT_AT,
        self::OPENED_AT,
        self::CLICKED_AT,
        self::METADATA,
    ];
}