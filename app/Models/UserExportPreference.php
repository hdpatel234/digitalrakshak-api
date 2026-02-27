<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExportPreference extends BaseModel
{
    
    protected $table = "user_export_preferences";
    
    const USER_ID = "user_id";
    const DEFAULT_FORMAT = "default_format";
    const PAPER_SIZE = "paper_size";
    const ORIENTATION = "orientation";
    const INCLUDE_TIMESTAMPS = "include_timestamps";
    const INCLUDE_METADATA = "include_metadata";
    const COMPRESSION = "compression";
    const EMAIL_ON_COMPLETE = "email_on_complete";
    protected $fillable = [
        self::USER_ID,
        self::DEFAULT_FORMAT,
        self::PAPER_SIZE,
        self::ORIENTATION,
        self::INCLUDE_TIMESTAMPS,
        self::INCLUDE_METADATA,
        self::COMPRESSION,
        self::EMAIL_ON_COMPLETE,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
