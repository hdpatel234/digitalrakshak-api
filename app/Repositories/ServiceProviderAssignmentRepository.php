<?php

namespace App\Repositories;

use App\Models\ServiceProviderAssignment;

class ServiceProviderAssignmentRepository extends BaseRepository
{
    public function __construct(ServiceProviderAssignment $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function providerConfigId()
    {
        return $this->model::PROVIDER_CONFIG_ID;
    }

    public function priority()
    {
        return $this->model::PRIORITY;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function isPrimary()
    {
        return $this->model::IS_PRIMARY;
    }

    public function isBackup()
    {
        return $this->model::IS_BACKUP;
    }

    public function fallbackThreshold()
    {
        return $this->model::FALLBACK_THRESHOLD;
    }

    public function cooldownPeriod()
    {
        return $this->model::COOLDOWN_PERIOD;
    }

    public function endpointOverride()
    {
        return $this->model::ENDPOINT_OVERRIDE;
    }

    public function methodOverride()
    {
        return $this->model::METHOD_OVERRIDE;
    }

    public function headersOverride()
    {
        return $this->model::HEADERS_OVERRIDE;
    }

    public function bodyTemplate()
    {
        return $this->model::BODY_TEMPLATE;
    }

    public function currentStatus()
    {
        return $this->model::CURRENT_STATUS;
    }

    public function failureCount()
    {
        return $this->model::FAILURE_COUNT;
    }

    public function lastFailureAt()
    {
        return $this->model::LAST_FAILURE_AT;
    }

    public function lastSuccessAt()
    {
        return $this->model::LAST_SUCCESS_AT;
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