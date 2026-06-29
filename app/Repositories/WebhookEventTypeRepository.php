<?php

namespace App\Repositories;

use App\Models\WebhookEventType;

class WebhookEventTypeRepository extends BaseRepository
{
    public function __construct(WebhookEventType $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function eventName()
    {
        return WebhookEventType::EVENT_NAME;
    }

    public function eventCode()
    {
        return WebhookEventType::EVENT_CODE;
    }

    public function category()
    {
        return WebhookEventType::CATEGORY;
    }

    public function description()
    {
        return WebhookEventType::DESCRIPTION;
    }

    public function samplePayload()
    {
        return WebhookEventType::SAMPLE_PAYLOAD;
    }

    public function isActive()
    {
        return WebhookEventType::IS_ACTIVE;
    }
    // functions
}