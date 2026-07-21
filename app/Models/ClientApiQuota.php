<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientApiQuota extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_api_quotas";
    
    const CLIENT_ID = "client_id";
    const PERIOD_START = "period_start";
    const PERIOD_END = "period_end";
    const REQUESTS_LIMIT = "requests_limit";
    const REQUESTS_USED = "requests_used";
    const REQUESTS_REMAINING = "requests_remaining";
    const RESET_AT = "reset_at";
    protected $fillable = [
        self::CLIENT_ID,
        self::PERIOD_START,
        self::PERIOD_END,
        self::REQUESTS_LIMIT,
        self::REQUESTS_USED,
        self::REQUESTS_REMAINING,
        self::RESET_AT,
    ];
}
