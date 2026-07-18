<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailRoutingRule extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "email_routing_rules";
    protected $casts = [
        self::IS_ACTIVE => 'boolean',
        self::DAYS_OF_WEEK => 'array',
    ];
    
    const RULE_NAME = "rule_name";
    const RULE_PRIORITY = "rule_priority";
    const IS_ACTIVE = "is_active";
    const MATCH_TYPE = "match_type";
    const MATCH_VALUE = "match_value";
    const MATCH_PATTERN = "match_pattern";
    const EMAIL_TYPE = "email_type";
    const ACTION_TYPE = "action_type";
    const SERVER_ID = "server_id";
    const SERVER_GROUP = "server_group";
    const FAILOVER_SERVER_ID = "failover_server_id";
    const MAX_RETRIES = "max_retries";
    const RETRY_DELAY_SECONDS = "retry_delay_seconds";
    const CLIENT_ID = "client_id";
    const TIME_START = "time_start";
    const TIME_END = "time_end";
    const DAYS_OF_WEEK = "days_of_week";
    const TIMES_USED = "times_used";
    const LAST_USED_AT = "last_used_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::RULE_NAME,
        self::RULE_PRIORITY,
        self::IS_ACTIVE,
        self::MATCH_TYPE,
        self::MATCH_VALUE,
        self::MATCH_PATTERN,
        self::EMAIL_TYPE,
        self::ACTION_TYPE,
        self::SERVER_ID,
        self::SERVER_GROUP,
        self::FAILOVER_SERVER_ID,
        self::MAX_RETRIES,
        self::RETRY_DELAY_SECONDS,
        self::CLIENT_ID,
        self::TIME_START,
        self::TIME_END,
        self::DAYS_OF_WEEK,
        self::TIMES_USED,
        self::LAST_USED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(EmailServer::class, self::SERVER_ID);
    }

    public function failoverServer(): BelongsTo
    {
        return $this->belongsTo(EmailServer::class, self::FAILOVER_SERVER_ID);
    }
}
