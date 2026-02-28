<?php

namespace App\Repositories;

use App\Models\ProviderResponseMapping;

class ProviderResponseMappingRepository extends BaseRepository
{
    public function __construct(ProviderResponseMapping $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceProviderAssignmentId()
    {
        return $this->model::SERVICE_PROVIDER_ASSIGNMENT_ID;
    }

    public function responseField()
    {
        return $this->model::RESPONSE_FIELD;
    }

    public function targetField()
    {
        return $this->model::TARGET_FIELD;
    }

    public function dataType()
    {
        return $this->model::DATA_TYPE;
    }

    public function path()
    {
        return $this->model::PATH;
    }

    public function transformFunction()
    {
        return $this->model::TRANSFORM_FUNCTION;
    }

    public function isVerificationResult()
    {
        return $this->model::IS_VERIFICATION_RESULT;
    }

    public function isRequired()
    {
        return $this->model::IS_REQUIRED;
    }
    // functions
}