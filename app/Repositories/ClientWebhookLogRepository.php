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
        return ClientWebhookLog::CLIENT_ID;
    }

    public function webhookId()
    {
        return ClientWebhookLog::WEBHOOK_ID;
    }

    public function eventType()
    {
        return ClientWebhookLog::EVENT_TYPE;
    }

    public function payload()
    {
        return ClientWebhookLog::PAYLOAD;
    }

    public function headers()
    {
        return ClientWebhookLog::HEADERS;
    }

    public function responseCode()
    {
        return ClientWebhookLog::RESPONSE_CODE;
    }

    public function responseBody()
    {
        return ClientWebhookLog::RESPONSE_BODY;
    }

    public function responseTimeMs()
    {
        return ClientWebhookLog::RESPONSE_TIME_MS;
    }

    public function attempt()
    {
        return ClientWebhookLog::ATTEMPT;
    }

    public function status()
    {
        return ClientWebhookLog::STATUS;
    }

    public function errorMessage()
    {
        return ClientWebhookLog::ERROR_MESSAGE;
    }

    public function nextRetryAt()
    {
        return ClientWebhookLog::NEXT_RETRY_AT;
    }
    // functions
}
