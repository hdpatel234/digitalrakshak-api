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
        return $this->model::SERVER_NAME;
    }

    public function serverTypeId()
    {
        return $this->model::SERVER_TYPE_ID;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function priority()
    {
        return $this->model::PRIORITY;
    }

    public function rateLimitPerMinute()
    {
        return $this->model::RATE_LIMIT_PER_MINUTE;
    }

    public function rateLimitPerHour()
    {
        return $this->model::RATE_LIMIT_PER_HOUR;
    }

    public function rateLimitPerDay()
    {
        return $this->model::RATE_LIMIT_PER_DAY;
    }

    public function defaultFromEmail()
    {
        return $this->model::DEFAULT_FROM_EMAIL;
    }

    public function defaultFromName()
    {
        return $this->model::DEFAULT_FROM_NAME;
    }

    public function defaultReplyTo()
    {
        return $this->model::DEFAULT_REPLY_TO;
    }

    public function serverGroup()
    {
        return $this->model::SERVER_GROUP;
    }

    public function weight()
    {
        return $this->model::WEIGHT;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function healthCheckAt()
    {
        return $this->model::HEALTH_CHECK_AT;
    }

    public function healthCheckStatus()
    {
        return $this->model::HEALTH_CHECK_STATUS;
    }

    public function lastError()
    {
        return $this->model::LAST_ERROR;
    }

    public function successCount()
    {
        return $this->model::SUCCESS_COUNT;
    }

    public function failureCount()
    {
        return $this->model::FAILURE_COUNT;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
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
