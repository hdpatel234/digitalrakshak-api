<?php

namespace App\Repositories;

use App\Models\ProviderPerformanceLog;

class ProviderPerformanceLogRepository extends BaseRepository
{
    public function __construct(ProviderPerformanceLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function assignmentId()
    {
        return $this->model::ASSIGNMENT_ID;
    }

    public function responseTimeMs()
    {
        return $this->model::RESPONSE_TIME_MS;
    }

    public function statusCode()
    {
        return $this->model::STATUS_CODE;
    }

    public function success()
    {
        return $this->model::SUCCESS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function loggedAt()
    {
        return $this->model::LOGGED_AT;
    }
    // functions
}