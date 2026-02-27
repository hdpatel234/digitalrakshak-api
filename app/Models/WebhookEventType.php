<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEventType extends BaseModel
{
    
    protected $table = "webhook_event_types";
    
    const EVENT_NAME = "event_name";
    const EVENT_CODE = "event_code";
    const CATEGORY = "category";
    const DESCRIPTION = "description";
    const SAMPLE_PAYLOAD = "sample_payload";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::EVENT_NAME,
        self::EVENT_CODE,
        self::CATEGORY,
        self::DESCRIPTION,
        self::SAMPLE_PAYLOAD,
        self::IS_ACTIVE,
    ];
}