<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailQueue extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "email_queue";
    protected $casts = [
        self::CC => 'array',
        self::BCC => 'array',
        self::PROVIDER_RESPONSE => 'array',
        self::SCHEDULED_AT => 'datetime',
        self::EXPIRES_AT => 'datetime',
        self::LAST_ATTEMPT_AT => 'datetime',
        self::SENT_AT => 'datetime',
        self::VARIABLES => 'array',
    ];
    
    const EMAIL_UID = "email_uid";
    const TO_EMAIL = "to_email";
    const TO_NAME = "to_name";
    const CC = "cc";
    const BCC = "bcc";
    const REPLY_TO = "reply_to";
    const SUBJECT = "subject";
    const BODY_HTML = "body_html";
    const BODY_TEXT = "body_text";
    const TEMPLATE_ID = "template_id";
    const VARIABLES = "variables";
    const EMAIL_TYPE = "email_type";
    const PRIORITY = "priority";
    const CLIENT_ID = "client_id";
    const CANDIDATE_ID = "candidate_id";
    const ORDER_ID = "order_id";
    const USER_ID = "user_id";
    const ASSIGNED_SERVER_ID = "assigned_server_id";
    const ROUTING_RULE_ID = "routing_rule_id";
    const STATUS = "status";
    const ATTEMPTS = "attempts";
    const MAX_ATTEMPTS = "max_attempts";
    const LAST_ATTEMPT_AT = "last_attempt_at";
    const SENT_AT = "sent_at";
    const MESSAGE_ID = "message_id";
    const PROVIDER_RESPONSE = "provider_response";
    const ERROR_MESSAGE = "error_message";
    const OPENS = "opens";
    const CLICKS = "clicks";
    const LAST_OPENED_AT = "last_opened_at";
    const SCHEDULED_AT = "scheduled_at";
    const EXPIRES_AT = "expires_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::EMAIL_UID,
        self::TO_EMAIL,
        self::TO_NAME,
        self::CC,
        self::BCC,
        self::REPLY_TO,
        self::SUBJECT,
        self::BODY_HTML,
        self::BODY_TEXT,
        self::TEMPLATE_ID,
        self::VARIABLES,
        self::EMAIL_TYPE,
        self::PRIORITY,
        self::CLIENT_ID,
        self::CANDIDATE_ID,
        self::ORDER_ID,
        self::USER_ID,
        self::ASSIGNED_SERVER_ID,
        self::ROUTING_RULE_ID,
        self::STATUS,
        self::ATTEMPTS,
        self::MAX_ATTEMPTS,
        self::LAST_ATTEMPT_AT,
        self::SENT_AT,
        self::MESSAGE_ID,
        self::PROVIDER_RESPONSE,
        self::ERROR_MESSAGE,
        self::OPENS,
        self::CLICKS,
        self::LAST_OPENED_AT,
        self::SCHEDULED_AT,
        self::EXPIRES_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }

    public function assignedServer(): BelongsTo
    {
        return $this->belongsTo(EmailServer::class, self::ASSIGNED_SERVER_ID);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EmailAttachment::class, EmailAttachment::EMAIL_QUEUE_ID);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, self::TEMPLATE_ID);
    }
}
