<?php

namespace App\Repositories;

use App\Models\ClientApiLog;

class ClientApiLogRepository extends BaseRepository
{
    public function __construct(ClientApiLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return ClientApiLog::CLIENT_ID;
    }

    public function apiKeyId()
    {
        return ClientApiLog::API_KEY_ID;
    }

    public function endpoint()
    {
        return ClientApiLog::ENDPOINT;
    }

    public function method()
    {
        return ClientApiLog::METHOD;
    }

    public function requestHeaders()
    {
        return ClientApiLog::REQUEST_HEADERS;
    }

    public function requestBody()
    {
        return ClientApiLog::REQUEST_BODY;
    }

    public function responseCode()
    {
        return ClientApiLog::RESPONSE_CODE;
    }

    public function responseBody()
    {
        return ClientApiLog::RESPONSE_BODY;
    }

    public function responseTimeMs()
    {
        return ClientApiLog::RESPONSE_TIME_MS;
    }

    public function ipAddress()
    {
        return ClientApiLog::IP_ADDRESS;
    }

    public function userAgent()
    {
        return ClientApiLog::USER_AGENT;
    }

    public function status()
    {
        return ClientApiLog::STATUS;
    }

    public function errorMessage()
    {
        return ClientApiLog::ERROR_MESSAGE;
    }
    // functions
}
