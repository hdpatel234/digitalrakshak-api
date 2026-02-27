<?php

namespace App\Repositories;

use App\Models\ClientWebhookLog;

class ClientWebhookLogRepository extends BaseRepository
{
    public function __construct(ClientWebhookLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function webhookId()
    {
        return $this->model::WEBHOOK_ID;
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

    public function responseCode()
    {
        return $this->model::RESPONSE_CODE;
    }

    public function responseBody()
    {
        return $this->model::RESPONSE_BODY;
    }

    public function responseTimeMs()
    {
        return $this->model::RESPONSE_TIME_MS;
    }

    public function attempt()
    {
        return $this->model::ATTEMPT;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function nextRetryAt()
    {
        return $this->model::NEXT_RETRY_AT;
    }
    // functions
}