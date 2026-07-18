<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSearchPreference extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_search_preferences";
    
    const USER_ID = "user_id";
    const DEFAULT_SEARCH_OPERATOR = "default_search_operator";
    const ITEMS_PER_PAGE = "items_per_page";
    const SAVE_RECENT_SEARCHES = "save_recent_searches";
    const MAX_RECENT_SEARCHES = "max_recent_searches";
    const SAVE_FILTERS = "save_filters";
    const DEFAULT_DATE_RANGE = "default_date_range";
    protected $fillable = [
        self::USER_ID,
        self::DEFAULT_SEARCH_OPERATOR,
        self::ITEMS_PER_PAGE,
        self::SAVE_RECENT_SEARCHES,
        self::MAX_RECENT_SEARCHES,
        self::SAVE_FILTERS,
        self::DEFAULT_DATE_RANGE,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
