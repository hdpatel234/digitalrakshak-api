<?php

namespace App\Repositories;

use App\Models\ServiceProviderResponseMapping;

class ServiceProviderResponseMappingRepository extends BaseRepository
{
    public function __construct(ServiceProviderResponseMapping $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceProviderAssignmentId()
    {
        return ServiceProviderResponseMapping::SERVICE_PROVIDER_ASSIGNMENT_ID;
    }

    public function responseField()
    {
        return ServiceProviderResponseMapping::RESPONSE_FIELD;
    }

    public function targetField()
    {
        return ServiceProviderResponseMapping::TARGET_FIELD;
    }

    public function dataType()
    {
        return ServiceProviderResponseMapping::DATA_TYPE;
    }

    public function path()
    {
        return ServiceProviderResponseMapping::PATH;
    }

    public function transformFunction()
    {
        return ServiceProviderResponseMapping::TRANSFORM_FUNCTION;
    }

    public function isVerificationResult()
    {
        return ServiceProviderResponseMapping::IS_VERIFICATION_RESULT;
    }

    public function isRequired()
    {
        return ServiceProviderResponseMapping::IS_REQUIRED;
    }

    // functions
}
