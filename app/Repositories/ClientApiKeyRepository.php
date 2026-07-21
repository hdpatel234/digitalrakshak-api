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
        return ClientApiKey::CLIENT_ID;
    }

    public function keyName()
    {
        return ClientApiKey::KEY_NAME;
    }

    public function apiKey()
    {
        return ClientApiKey::API_KEY;
    }

    public function apiSecret()
    {
        return ClientApiKey::API_SECRET;
    }

    public function keyType()
    {
        return ClientApiKey::KEY_TYPE;
    }

    public function permissions()
    {
        return ClientApiKey::PERMISSIONS;
    }

    public function ipWhitelist()
    {
        return ClientApiKey::IP_WHITELIST;
    }

    public function rateLimit()
    {
        return ClientApiKey::RATE_LIMIT;
    }

    public function rateLimitPerDay()
    {
        return ClientApiKey::RATE_LIMIT_PER_DAY;
    }

    public function expiresAt()
    {
        return ClientApiKey::EXPIRES_AT;
    }

    public function lastUsedAt()
    {
        return ClientApiKey::LAST_USED_AT;
    }

    public function lastUsedIp()
    {
        return ClientApiKey::LAST_USED_IP;
    }

    public function totalRequests()
    {
        return ClientApiKey::TOTAL_REQUESTS;
    }

    public function status()
    {
        return ClientApiKey::STATUS;
    }

    public function createdBy()
    {
        return ClientApiKey::CREATED_BY;
    }

    public function updatedBy()
    {
        return ClientApiKey::UPDATED_BY;
    }
    // functions
}
