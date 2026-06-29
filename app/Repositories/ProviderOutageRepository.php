<?php

namespace App\Repositories;

use App\Models\ProviderOutage;

class ProviderOutageRepository extends BaseRepository
{
    public function __construct(ProviderOutage $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return ProviderOutage::PROVIDER_ID;
    }

    public function serviceId()
    {
        return ProviderOutage::SERVICE_ID;
    }

    public function outageType()
    {
        return ProviderOutage::OUTAGE_TYPE;
    }

    public function startedAt()
    {
        return ProviderOutage::STARTED_AT;
    }

    public function endedAt()
    {
        return ProviderOutage::ENDED_AT;
    }

    public function durationMinutes()
    {
        return ProviderOutage::DURATION_MINUTES;
    }

    public function affectedServices()
    {
        return ProviderOutage::AFFECTED_SERVICES;
    }

    public function rootCause()
    {
        return ProviderOutage::ROOT_CAUSE;
    }

    public function resolution()
    {
        return ProviderOutage::RESOLUTION;
    }
    // functions
}