<?php

namespace App\Repositories;

use App\Models\EmailServer;

class EmailServerRepository extends BaseRepository
{
    public function __construct(EmailServer $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serverName()
    {
        return EmailServer::SERVER_NAME;
    }

    public function serverTypeId()
    {
        return EmailServer::SERVER_TYPE_ID;
    }

    public function isDefault()
    {
        return EmailServer::IS_DEFAULT;
    }

    public function priority()
    {
        return EmailServer::PRIORITY;
    }

    public function host()
    {
        return EmailServer::HOST;
    }

    public function port()
    {
        return EmailServer::PORT;
    }

    public function encryption()
    {
        return EmailServer::ENCRYPTION;
    }

    public function username()
    {
        return EmailServer::USERNAME;
    }

    public function password()
    {
        return EmailServer::PASSWORD;
    }

    public function timeout()
    {
        return EmailServer::TIMEOUT;
    }

    public function verifySsl()
    {
        return EmailServer::VERIFY_SSL;
    }

    public function authType()
    {
        return EmailServer::AUTH_TYPE;
    }

    public function apiKey()
    {
        return EmailServer::API_KEY;
    }

    public function apiSecret()
    {
        return EmailServer::API_SECRET;
    }

    public function apiEndpoint()
    {
        return EmailServer::API_ENDPOINT;
    }

    public function domain()
    {
        return EmailServer::DOMAIN;
    }

    public function rateLimitPerMinute()
    {
        return EmailServer::RATE_LIMIT_PER_MINUTE;
    }

    public function rateLimitPerHour()
    {
        return EmailServer::RATE_LIMIT_PER_HOUR;
    }

    public function rateLimitPerDay()
    {
        return EmailServer::RATE_LIMIT_PER_DAY;
    }

    public function defaultFromEmail()
    {
        return EmailServer::DEFAULT_FROM_EMAIL;
    }

    public function defaultFromName()
    {
        return EmailServer::DEFAULT_FROM_NAME;
    }

    public function defaultReplyTo()
    {
        return EmailServer::DEFAULT_REPLY_TO;
    }

    public function serverGroup()
    {
        return EmailServer::SERVER_GROUP;
    }

    public function weight()
    {
        return EmailServer::WEIGHT;
    }

    public function status()
    {
        return EmailServer::STATUS;
    }

    public function healthCheckAt()
    {
        return EmailServer::HEALTH_CHECK_AT;
    }

    public function healthCheckStatus()
    {
        return EmailServer::HEALTH_CHECK_STATUS;
    }

    public function lastError()
    {
        return EmailServer::LAST_ERROR;
    }

    public function successCount()
    {
        return EmailServer::SUCCESS_COUNT;
    }

    public function failureCount()
    {
        return EmailServer::FAILURE_COUNT;
    }

    public function lastUsedAt()
    {
        return EmailServer::LAST_USED_AT;
    }

    public function createdBy()
    {
        return EmailServer::CREATED_BY;
    }

    public function updatedBy()
    {
        return EmailServer::UPDATED_BY;
    }
    // functions
}