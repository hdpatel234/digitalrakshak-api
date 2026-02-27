<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserPrivacySetting extends BaseModel
{
    
    protected $table = "user_privacy_settings";
    
    const USER_ID = "user_id";
    const PROFILE_VISIBILITY = "profile_visibility";
    const SHOW_EMAIL = "show_email";
    const SHOW_PHONE = "show_phone";
    const SHOW_ACTIVITY = "show_activity";
    const ALLOW_DATA_COLLECTION = "allow_data_collection";
    const ALLOW_MARKETING_EMAILS = "allow_marketing_emails";
    const ALLOW_ANALYTICS = "allow_analytics";
    const COOKIE_CONSENT = "cookie_consent";
    const DATA_RETENTION_PREFERENCE = "data_retention_preference";
    protected $fillable = [
        self::USER_ID,
        self::PROFILE_VISIBILITY,
        self::SHOW_EMAIL,
        self::SHOW_PHONE,
        self::SHOW_ACTIVITY,
        self::ALLOW_DATA_COLLECTION,
        self::ALLOW_MARKETING_EMAILS,
        self::ALLOW_ANALYTICS,
        self::COOKIE_CONSENT,
        self::DATA_RETENTION_PREFERENCE,
    ];
}