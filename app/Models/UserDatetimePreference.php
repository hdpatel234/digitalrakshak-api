<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDatetimePreference extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_datetime_preferences";
    
    const USER_ID = "user_id";
    const TIMEZONE = "timezone";
    const DATE_FORMAT = "date_format";
    const TIME_FORMAT = "time_format";
    const FIRST_DAY_OF_WEEK = "first_day_of_week";
    const WEEK_STARTS_ON = "week_starts_on";
    const SHOW_WEEK_NUMBERS = "show_week_numbers";
    const CALENDAR_VIEW = "calendar_view";
    const WORKING_HOURS_START = "working_hours_start";
    const WORKING_HOURS_END = "working_hours_end";
    const WORKING_DAYS = "working_days";
    protected $fillable = [
        self::USER_ID,
        self::TIMEZONE,
        self::DATE_FORMAT,
        self::TIME_FORMAT,
        self::FIRST_DAY_OF_WEEK,
        self::WEEK_STARTS_ON,
        self::SHOW_WEEK_NUMBERS,
        self::CALENDAR_VIEW,
        self::WORKING_HOURS_START,
        self::WORKING_HOURS_END,
        self::WORKING_DAYS,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
