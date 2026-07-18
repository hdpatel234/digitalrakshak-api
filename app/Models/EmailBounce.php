<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailBounce extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "email_bounces";
    
    const EMAIL = "email";
    const BOUNCE_TYPE = "bounce_type";
    const REASON = "reason";
    const BOUNCED_AT = "bounced_at";
    const UNSUBSCRIBED_AT = "unsubscribed_at";
    const BLOCKED_UNTIL = "blocked_until";
    const BOUNCE_COUNT = "bounce_count";
    protected $fillable = [
        self::EMAIL,
        self::BOUNCE_TYPE,
        self::REASON,
        self::BOUNCED_AT,
        self::UNSUBSCRIBED_AT,
        self::BLOCKED_UNTIL,
        self::BOUNCE_COUNT,
    ];
}