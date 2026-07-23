<?php

namespace App\Repositories;

use App\Models\ServiceProviderOutage;

class ServiceProviderOutageRepository extends BaseRepository
{
    public function __construct(ServiceProviderOutage $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return ServiceProviderOutage::PROVIDER_ID;
    }

    public function serviceId()
    {
        return ServiceProviderOutage::SERVICE_ID;
    }

    public function outageType()
    {
        return ServiceProviderOutage::OUTAGE_TYPE;
    }

    public function startedAt()
    {
        return ServiceProviderOutage::STARTED_AT;
    }

    public function endedAt()
    {
        return ServiceProviderOutage::ENDED_AT;
    }

    public function durationMinutes()
    {
        return ServiceProviderOutage::DURATION_MINUTES;
    }

    public function affectedServices()
    {
        return ServiceProviderOutage::AFFECTED_SERVICES;
    }

    public function rootCause()
    {
        return ServiceProviderOutage::ROOT_CAUSE;
    }

    public function resolution()
    {
        return ServiceProviderOutage::RESOLUTION;
    }
    // functions
}
