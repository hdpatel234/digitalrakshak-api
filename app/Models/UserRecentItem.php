<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRecentItem extends BaseModel
{
    
    protected $table = "user_recent_items";
    
    const USER_ID = "user_id";
    const ITEM_TYPE = "item_type";
    const ITEM_ID = "item_id";
    const URL = "url";
    const TITLE = "title";
    const METADATA = "metadata";
    const LAST_ACCESSED_AT = "last_accessed_at";
    const ACCESS_COUNT = "access_count";
    protected $fillable = [
        self::USER_ID,
        self::ITEM_TYPE,
        self::ITEM_ID,
        self::URL,
        self::TITLE,
        self::METADATA,
        self::LAST_ACCESSED_AT,
        self::ACCESS_COUNT,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
