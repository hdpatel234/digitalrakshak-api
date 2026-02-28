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
        return $this->model::PROVIDER_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function outageType()
    {
        return $this->model::OUTAGE_TYPE;
    }

    public function startedAt()
    {
        return $this->model::STARTED_AT;
    }

    public function endedAt()
    {
        return $this->model::ENDED_AT;
    }

    public function durationMinutes()
    {
        return $this->model::DURATION_MINUTES;
    }

    public function affectedServices()
    {
        return $this->model::AFFECTED_SERVICES;
    }

    public function rootCause()
    {
        return $this->model::ROOT_CAUSE;
    }

    public function resolution()
    {
        return $this->model::RESOLUTION;
    }
    // functions
}