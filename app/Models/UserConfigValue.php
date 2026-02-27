<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConfigValue extends BaseModel
{
    
    protected $table = "user_config_values";
    
    const USER_ID = "user_id";
    const CONFIG_ID = "config_id";
    const VALUE = "value";
    protected $fillable = [
        self::USER_ID,
        self::CONFIG_ID,
        self::VALUE,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
