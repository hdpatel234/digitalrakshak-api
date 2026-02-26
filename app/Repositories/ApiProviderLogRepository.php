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
        return $this->model::API_PROVIDER_ID;
    }

    public function endpoint()
    {
        return $this->model::ENDPOINT;
    }

    public function method()
    {
        return $this->model::METHOD;
    }

    public function request()
    {
        return $this->model::REQUEST;
    }

    public function response()
    {
        return $this->model::RESPONSE;
    }

    public function responseCode()
    {
        return $this->model::RESPONSE_CODE;
    }

    public function duration()
    {
        return $this->model::DURATION;
    }

    public function isSuccessful()
    {
        return $this->model::IS_SUCCESSFUL;
    }
    // functions
}