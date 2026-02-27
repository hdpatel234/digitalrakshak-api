<?php

namespace App\Repositories;

use App\Models\ClientWebhook;

class ClientWebhookRepository extends BaseRepository
{
    public function __construct(ClientWebhook $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function webhookName()
    {
        return $this->model::WEBHOOK_NAME;
    }

    public function webhookUrl()
    {
        return $this->model::WEBHOOK_URL;
    }

    public function webhookSecret()
    {
        return $this->model::WEBHOOK_SECRET;
    }

    public function events()
    {
        return $this->model::EVENTS;
    }

    public function headers()
    {
        return $this->model::HEADERS;
    }

    public function format()
    {
        return $this->model::FORMAT;
    }

    public function maxRetries()
    {
        return $this->model::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return $this->model::RETRY_DELAY_SECONDS;
    }

    public function timeoutSeconds()
    {
        return $this->model::TIMEOUT_SECONDS;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function lastTriggeredAt()
    {
        return $this->model::LAST_TRIGGERED_AT;
    }

    public function lastSuccessAt()
    {
        return $this->model::LAST_SUCCESS_AT;
    }

    public function lastFailureAt()
    {
        return $this->model::LAST_FAILURE_AT;
    }

    public function lastError()
    {
        return $this->model::LAST_ERROR;
    }

    public function totalSuccess()
    {
        return $this->model::TOTAL_SUCCESS;
    }

    public function totalFailures()
    {
        return $this->model::TOTAL_FAILURES;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}