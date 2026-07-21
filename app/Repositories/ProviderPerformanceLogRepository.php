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
        return ProviderPerformanceLog::PROVIDER_ID;
    }

    public function serviceId()
    {
        return ProviderPerformanceLog::SERVICE_ID;
    }

    public function assignmentId()
    {
        return ProviderPerformanceLog::ASSIGNMENT_ID;
    }

    public function responseTimeMs()
    {
        return ProviderPerformanceLog::RESPONSE_TIME_MS;
    }

    public function statusCode()
    {
        return ProviderPerformanceLog::STATUS_CODE;
    }

    public function success()
    {
        return ProviderPerformanceLog::SUCCESS;
    }

    public function errorMessage()
    {
        return ProviderPerformanceLog::ERROR_MESSAGE;
    }

    public function loggedAt()
    {
        return ProviderPerformanceLog::LOGGED_AT;
    }
    // functions
}
