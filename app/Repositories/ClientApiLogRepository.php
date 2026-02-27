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
        return $this->model::CLIENT_ID;
    }

    public function apiKeyId()
    {
        return $this->model::API_KEY_ID;
    }

    public function endpoint()
    {
        return $this->model::ENDPOINT;
    }

    public function method()
    {
        return $this->model::METHOD;
    }

    public function requestHeaders()
    {
        return $this->model::REQUEST_HEADERS;
    }

    public function requestBody()
    {
        return $this->model::REQUEST_BODY;
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

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }

    public function userAgent()
    {
        return $this->model::USER_AGENT;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }
    // functions
}