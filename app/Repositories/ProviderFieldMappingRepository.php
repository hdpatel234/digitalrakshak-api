<?php

namespace App\Repositories;

use App\Models\ProviderFieldMapping;

class ProviderFieldMappingRepository extends BaseRepository
{
    public function __construct(ProviderFieldMapping $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceProviderAssignmentId()
    {
        return ProviderFieldMapping::SERVICE_PROVIDER_ASSIGNMENT_ID;
    }

    public function serviceFieldId()
    {
        return ProviderFieldMapping::SERVICE_FIELD_ID;
    }

    public function providerFieldName()
    {
        return ProviderFieldMapping::PROVIDER_FIELD_NAME;
    }

    public function fieldPath()
    {
        return ProviderFieldMapping::FIELD_PATH;
    }

    public function transformFunction()
    {
        return ProviderFieldMapping::TRANSFORM_FUNCTION;
    }

    public function isRequired()
    {
        return ProviderFieldMapping::IS_REQUIRED;
    }

    public function defaultValue()
    {
        return ProviderFieldMapping::DEFAULT_VALUE;
    }
    // functions
}