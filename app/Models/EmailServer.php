<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailServer extends BaseModel
{
    use SoftDeletes;
    
    protected $table = "email_servers";
    
    const SERVER_NAME = "server_name";
    const SERVER_TYPE_ID = "server_type_id";
    const IS_DEFAULT = "is_default";
    const PRIORITY = "priority";
    const RATE_LIMIT_PER_MINUTE = "rate_limit_per_minute";
    const RATE_LIMIT_PER_HOUR = "rate_limit_per_hour";
    const RATE_LIMIT_PER_DAY = "rate_limit_per_day";
    const DEFAULT_FROM_EMAIL = "default_from_email";
    const DEFAULT_FROM_NAME = "default_from_name";
    const DEFAULT_REPLY_TO = "default_reply_to";
    const SERVER_GROUP = "server_group";
    const WEIGHT = "weight";
    const STATUS = "status";
    const HEALTH_CHECK_AT = "health_check_at";
    const HEALTH_CHECK_STATUS = "health_check_status";
    const LAST_ERROR = "last_error";
    const SUCCESS_COUNT = "success_count";
    const FAILURE_COUNT = "failure_count";
    const LAST_USED_AT = "last_used_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::SERVER_NAME,
        self::SERVER_TYPE_ID,
        self::IS_DEFAULT,
        self::PRIORITY,
        self::RATE_LIMIT_PER_MINUTE,
        self::RATE_LIMIT_PER_HOUR,
        self::RATE_LIMIT_PER_DAY,
        self::DEFAULT_FROM_EMAIL,
        self::DEFAULT_FROM_NAME,
        self::DEFAULT_REPLY_TO,
        self::SERVER_GROUP,
        self::WEIGHT,
        self::STATUS,
        self::HEALTH_CHECK_AT,
        self::HEALTH_CHECK_STATUS,
        self::LAST_ERROR,
        self::SUCCESS_COUNT,
        self::FAILURE_COUNT,
        self::LAST_USED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
    public function serverType()
    {
        return $this->belongsTo(EmailServerType::class, 'server_type_id', 'id');
    }

    public function configurationValues()
    {
        return $this->hasMany(EmailServerConfigurationValue::class, 'email_server_id');
    }

    public function getConfig(string $key, $default = null)
    {
        if (!$this->relationLoaded('configurationValues')) {
            $this->load('configurationValues.field');
        }

        $config = $this->configurationValues->first(function ($value) use ($key) {
            return $value->field && $value->field->field_name === $key;
        });

        return $config ? $config->field_value : $default;
    }
}