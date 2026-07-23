<?php

namespace App\Repositories;

use App\Models\ServiceProviderFieldMapping;

class ServiceProviderFieldMappingRepository extends BaseRepository
{
    public function __construct(ServiceProviderFieldMapping $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceProviderAssignmentId()
    {
        return ServiceProviderFieldMapping::SERVICE_PROVIDER_ASSIGNMENT_ID;
    }

    public function serviceFieldId()
    {
        return ServiceProviderFieldMapping::SERVICE_FIELD_ID;
    }

    public function providerFieldName()
    {
        return ServiceProviderFieldMapping::PROVIDER_FIELD_NAME;
    }

    public function fieldPath()
    {
        return ServiceProviderFieldMapping::FIELD_PATH;
    }

    public function transformFunction()
    {
        return ServiceProviderFieldMapping::TRANSFORM_FUNCTION;
    }

    public function isRequired()
    {
        return ServiceProviderFieldMapping::IS_REQUIRED;
    }

    public function defaultValue()
    {
        return ServiceProviderFieldMapping::DEFAULT_VALUE;
    }
    // functions
}
