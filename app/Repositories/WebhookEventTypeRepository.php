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
        return $this->model::EVENT_NAME;
    }

    public function eventCode()
    {
        return $this->model::EVENT_CODE;
    }

    public function category()
    {
        return $this->model::CATEGORY;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function samplePayload()
    {
        return $this->model::SAMPLE_PAYLOAD;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
}