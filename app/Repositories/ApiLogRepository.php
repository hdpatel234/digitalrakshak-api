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
        return ApiLog::SERVICE_ID;
    }

    public function orderItemId()
    {
        return ApiLog::ORDER_ITEM_ID;
    }

    public function endpoint()
    {
        return ApiLog::ENDPOINT;
    }

    public function method()
    {
        return ApiLog::METHOD;
    }

    public function requestData()
    {
        return ApiLog::REQUEST_DATA;
    }

    public function responseData()
    {
        return ApiLog::RESPONSE_DATA;
    }

    public function httpStatus()
    {
        return ApiLog::HTTP_STATUS;
    }

    public function status()
    {
        return ApiLog::STATUS;
    }

    public function errorMessage()
    {
        return ApiLog::ERROR_MESSAGE;
    }

    public function durationMs()
    {
        return ApiLog::DURATION_MS;
    }

    public function ipAddress()
    {
        return ApiLog::IP_ADDRESS;
    }
    // functions
}