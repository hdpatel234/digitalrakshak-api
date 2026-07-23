<?php

namespace App\Repositories;

use App\Models\ServiceProviderPerformanceLog;

class ServiceProviderPerformanceLogRepository extends BaseRepository
{
    public function __construct(ServiceProviderPerformanceLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return ServiceProviderPerformanceLog::PROVIDER_ID;
    }

    public function serviceId()
    {
        return ServiceProviderPerformanceLog::SERVICE_ID;
    }

    public function assignmentId()
    {
        return ServiceProviderPerformanceLog::ASSIGNMENT_ID;
    }

    public function responseTimeMs()
    {
        return ServiceProviderPerformanceLog::RESPONSE_TIME_MS;
    }

    public function statusCode()
    {
        return ServiceProviderPerformanceLog::STATUS_CODE;
    }

    public function success()
    {
        return ServiceProviderPerformanceLog::SUCCESS;
    }

    public function errorMessage()
    {
        return ServiceProviderPerformanceLog::ERROR_MESSAGE;
    }

    public function loggedAt()
    {
        return ServiceProviderPerformanceLog::LOGGED_AT;
    }

    // functions
}
