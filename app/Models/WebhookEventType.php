<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEventType extends BaseModel
{
    use SoftDeletes;
    protected $table = "webhook_event_types";
    const EVENT_NAME = "event_name";
    const EVENT_CODE = "event_code";
    const CATEGORY = "category";
    const DESCRIPTION = "description";
    const SAMPLE_PAYLOAD = "sample_payload";
    protected $fillable = [
        self::EVENT_NAME,
        self::EVENT_CODE,
        self::CATEGORY,
        self::DESCRIPTION,
        self::SAMPLE_PAYLOAD,
        self::STATUS,
    ];
}
