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
        return ProviderResponseMapping::SERVICE_PROVIDER_ASSIGNMENT_ID;
    }

    public function responseField()
    {
        return ProviderResponseMapping::RESPONSE_FIELD;
    }

    public function targetField()
    {
        return ProviderResponseMapping::TARGET_FIELD;
    }

    public function dataType()
    {
        return ProviderResponseMapping::DATA_TYPE;
    }

    public function path()
    {
        return ProviderResponseMapping::PATH;
    }

    public function transformFunction()
    {
        return ProviderResponseMapping::TRANSFORM_FUNCTION;
    }

    public function isVerificationResult()
    {
        return ProviderResponseMapping::IS_VERIFICATION_RESULT;
    }

    public function isRequired()
    {
        return ProviderResponseMapping::IS_REQUIRED;
    }
    // functions
}
