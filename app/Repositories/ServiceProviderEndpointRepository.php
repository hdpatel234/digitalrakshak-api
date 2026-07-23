<?php

namespace App\Repositories;

use App\Enums\BaseStatus;
use App\Models\ServiceProviderEndpoint;

class ServiceProviderEndpointRepository extends BaseRepository
{
    public function __construct(ServiceProviderEndpoint $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function configId()
    {
        return ServiceProviderEndpoint::CONFIG_ID;
    }

    public function apiName()
    {
        return ServiceProviderEndpoint::API_NAME;
    }

    public function apiCode()
    {
        return ServiceProviderEndpoint::API_CODE;
    }

    public function endpointPath()
    {
        return ServiceProviderEndpoint::ENDPOINT_PATH;
    }

    public function httpMethod()
    {
        return ServiceProviderEndpoint::HTTP_METHOD;
    }

    public function contentType()
    {
        return ServiceProviderEndpoint::CONTENT_TYPE;
    }

    public function customHeaders()
    {
        return ServiceProviderEndpoint::CUSTOM_HEADERS;
    }

    public function requestSchema()
    {
        return ServiceProviderEndpoint::REQUEST_SCHEMA;
    }

    public function responseSchema()
    {
        return ServiceProviderEndpoint::RESPONSE_SCHEMA;
    }

    public function status()
    {
        return ServiceProviderEndpoint::STATUS;
    }

    public function getEndpointByCode(string $apiCode, int $configId)
    {
        return $this->query()
            ->where($this->apiCode(), $apiCode)
            ->where($this->configId(), $configId)
            ->where($this->status(), BaseStatus::ACTIVE)
            ->first();
    }
}
