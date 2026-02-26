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
        return $this->model::SOURCE;
    }

    public function platform()
    {
        return $this->model::PLATFORM;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function eventType()
    {
        return $this->model::EVENT_TYPE;
    }

    public function payload()
    {
        return $this->model::PAYLOAD;
    }

    public function headers()
    {
        return $this->model::HEADERS;
    }

    public function processed()
    {
        return $this->model::PROCESSED;
    }

    public function processedAt()
    {
        return $this->model::PROCESSED_AT;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }
    // functions
}