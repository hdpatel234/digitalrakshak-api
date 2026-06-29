<?php

namespace App\Repositories;

use App\Models\ApiProviderLog;

class ApiProviderLogRepository extends BaseRepository
{
    public function __construct(ApiProviderLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function apiProviderId()
    {
        return ApiProviderLog::API_PROVIDER_ID;
    }

    public function endpoint()
    {
        return ApiProviderLog::ENDPOINT;
    }

    public function method()
    {
        return ApiProviderLog::METHOD;
    }

    public function request()
    {
        return ApiProviderLog::REQUEST;
    }

    public function response()
    {
        return ApiProviderLog::RESPONSE;
    }

    public function responseCode()
    {
        return ApiProviderLog::RESPONSE_CODE;
    }

    public function duration()
    {
        return ApiProviderLog::DURATION;
    }

    public function isSuccessful()
    {
        return ApiProviderLog::IS_SUCCESSFUL;
    }
    // functions
}