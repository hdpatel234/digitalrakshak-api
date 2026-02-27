<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserSavedSearch extends BaseModel
{
    
    protected $table = "user_saved_searches";
    
    const USER_ID = "user_id";
    const SEARCH_NAME = "search_name";
    const ENTITY_TYPE = "entity_type";
    const FILTERS = "filters";
    const COLUMNS = "columns";
    const SORT = "sort";
    const IS_SHARED = "is_shared";
    protected $fillable = [
        self::USER_ID,
        self::SEARCH_NAME,
        self::ENTITY_TYPE,
        self::FILTERS,
        self::COLUMNS,
        self::SORT,
        self::IS_SHARED,
    ];
}