<?php

namespace App\Repositories;

use App\Models\ClientApiKey;

class ClientApiKeyRepository extends BaseRepository
{
    public function __construct(ClientApiKey $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function keyName()
    {
        return $this->model::KEY_NAME;
    }

    public function apiKey()
    {
        return $this->model::API_KEY;
    }

    public function apiSecret()
    {
        return $this->model::API_SECRET;
    }

    public function keyType()
    {
        return $this->model::KEY_TYPE;
    }

    public function permissions()
    {
        return $this->model::PERMISSIONS;
    }

    public function ipWhitelist()
    {
        return $this->model::IP_WHITELIST;
    }

    public function rateLimit()
    {
        return $this->model::RATE_LIMIT;
    }

    public function rateLimitPerDay()
    {
        return $this->model::RATE_LIMIT_PER_DAY;
    }

    public function expiresAt()
    {
        return $this->model::EXPIRES_AT;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
    }

    public function lastUsedIp()
    {
        return $this->model::LAST_USED_IP;
    }

    public function totalRequests()
    {
        return $this->model::TOTAL_REQUESTS;
    }

    public function status()
    {
        return $this->model::STATUS;
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