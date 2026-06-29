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
        return ServiceProviderAssignment::SERVICE_ID;
    }

    public function providerId()
    {
        return ServiceProviderAssignment::PROVIDER_ID;
    }

    public function providerConfigId()
    {
        return ServiceProviderAssignment::PROVIDER_CONFIG_ID;
    }

    public function priority()
    {
        return ServiceProviderAssignment::PRIORITY;
    }

    public function isActive()
    {
        return ServiceProviderAssignment::IS_ACTIVE;
    }

    public function isDefault()
    {
        return ServiceProviderAssignment::IS_DEFAULT;
    }

    public function isPrimary()
    {
        return ServiceProviderAssignment::IS_PRIMARY;
    }

    public function isBackup()
    {
        return ServiceProviderAssignment::IS_BACKUP;
    }

    public function fallbackThreshold()
    {
        return ServiceProviderAssignment::FALLBACK_THRESHOLD;
    }

    public function cooldownPeriod()
    {
        return ServiceProviderAssignment::COOLDOWN_PERIOD;
    }

    public function endpointOverride()
    {
        return ServiceProviderAssignment::ENDPOINT_OVERRIDE;
    }

    public function methodOverride()
    {
        return ServiceProviderAssignment::METHOD_OVERRIDE;
    }

    public function headersOverride()
    {
        return ServiceProviderAssignment::HEADERS_OVERRIDE;
    }

    public function bodyTemplate()
    {
        return ServiceProviderAssignment::BODY_TEMPLATE;
    }

    public function currentStatus()
    {
        return ServiceProviderAssignment::CURRENT_STATUS;
    }

    public function failureCount()
    {
        return ServiceProviderAssignment::FAILURE_COUNT;
    }

    public function lastFailureAt()
    {
        return ServiceProviderAssignment::LAST_FAILURE_AT;
    }

    public function lastSuccessAt()
    {
        return ServiceProviderAssignment::LAST_SUCCESS_AT;
    }

    public function lastUsedAt()
    {
        return ServiceProviderAssignment::LAST_USED_AT;
    }

    public function createdBy()
    {
        return ServiceProviderAssignment::CREATED_BY;
    }

    public function updatedBy()
    {
        return ServiceProviderAssignment::UPDATED_BY;
    }
    // functions
}