<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotificationPreference extends BaseModel
{
    
    protected $table = "user_notification_preferences";
    
    const USER_ID = "user_id";
    const NOTIFICATION_TYPE = "notification_type";
    const EVENT_TYPE = "event_type";
    const ENABLED = "enabled";
    const CHANNELS = "channels";
    const QUIET_HOURS_START = "quiet_hours_start";
    const QUIET_HOURS_END = "quiet_hours_end";
    const DIGEST_FREQUENCY = "digest_frequency";
    protected $fillable = [
        self::USER_ID,
        self::NOTIFICATION_TYPE,
        self::EVENT_TYPE,
        self::ENABLED,
        self::CHANNELS,
        self::QUIET_HOURS_START,
        self::QUIET_HOURS_END,
        self::DIGEST_FREQUENCY,
    ];
}