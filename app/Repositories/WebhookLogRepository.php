<?php

namespace App\Repositories;

use App\Models\WebhookLog;

class WebhookLogRepository extends BaseRepository
{
    public function __construct(WebhookLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function source()
    {
        return WebhookLog::SOURCE;
    }

    public function platform()
    {
        return WebhookLog::PLATFORM;
    }

    public function clientId()
    {
        return WebhookLog::CLIENT_ID;
    }

    public function eventType()
    {
        return WebhookLog::EVENT_TYPE;
    }

    public function payload()
    {
        return WebhookLog::PAYLOAD;
    }

    public function headers()
    {
        return WebhookLog::HEADERS;
    }

    public function processed()
    {
        return WebhookLog::PROCESSED;
    }

    public function processedAt()
    {
        return WebhookLog::PROCESSED_AT;
    }

    public function errorMessage()
    {
        return WebhookLog::ERROR_MESSAGE;
    }
    // functions
}
