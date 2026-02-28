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
        return $this->model::SERVICE_PROVIDER_ASSIGNMENT_ID;
    }

    public function serviceFieldId()
    {
        return $this->model::SERVICE_FIELD_ID;
    }

    public function providerFieldName()
    {
        return $this->model::PROVIDER_FIELD_NAME;
    }

    public function fieldPath()
    {
        return $this->model::FIELD_PATH;
    }

    public function transformFunction()
    {
        return $this->model::TRANSFORM_FUNCTION;
    }

    public function isRequired()
    {
        return $this->model::IS_REQUIRED;
    }

    public function defaultValue()
    {
        return $this->model::DEFAULT_VALUE;
    }
    // functions
}