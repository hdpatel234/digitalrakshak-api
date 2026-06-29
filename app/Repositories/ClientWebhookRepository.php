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
        return ClientWebhook::CLIENT_ID;
    }

    public function webhookName()
    {
        return ClientWebhook::WEBHOOK_NAME;
    }

    public function webhookUrl()
    {
        return ClientWebhook::WEBHOOK_URL;
    }

    public function webhookSecret()
    {
        return ClientWebhook::WEBHOOK_SECRET;
    }

    public function events()
    {
        return ClientWebhook::EVENTS;
    }

    public function headers()
    {
        return ClientWebhook::HEADERS;
    }

    public function format()
    {
        return ClientWebhook::FORMAT;
    }

    public function maxRetries()
    {
        return ClientWebhook::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return ClientWebhook::RETRY_DELAY_SECONDS;
    }

    public function timeoutSeconds()
    {
        return ClientWebhook::TIMEOUT_SECONDS;
    }

    public function isActive()
    {
        return ClientWebhook::IS_ACTIVE;
    }

    public function lastTriggeredAt()
    {
        return ClientWebhook::LAST_TRIGGERED_AT;
    }

    public function lastSuccessAt()
    {
        return ClientWebhook::LAST_SUCCESS_AT;
    }

    public function lastFailureAt()
    {
        return ClientWebhook::LAST_FAILURE_AT;
    }

    public function lastError()
    {
        return ClientWebhook::LAST_ERROR;
    }

    public function totalSuccess()
    {
        return ClientWebhook::TOTAL_SUCCESS;
    }

    public function totalFailures()
    {
        return ClientWebhook::TOTAL_FAILURES;
    }

    public function createdBy()
    {
        return ClientWebhook::CREATED_BY;
    }

    public function updatedBy()
    {
        return ClientWebhook::UPDATED_BY;
    }
    // functions
}