<?php

namespace App\Repositories;

use App\Models\ApiLog;

class ApiLogRepository extends BaseRepository
{
    public function __construct(ApiLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function orderItemId()
    {
        return $this->model::ORDER_ITEM_ID;
    }

    public function endpoint()
    {
        return $this->model::ENDPOINT;
    }

    public function method()
    {
        return $this->model::METHOD;
    }

    public function requestData()
    {
        return $this->model::REQUEST_DATA;
    }

    public function responseData()
    {
        return $this->model::RESPONSE_DATA;
    }

    public function httpStatus()
    {
        return $this->model::HTTP_STATUS;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function durationMs()
    {
        return $this->model::DURATION_MS;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }
    // functions
}