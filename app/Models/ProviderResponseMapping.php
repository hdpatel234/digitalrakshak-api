<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderResponseMapping extends BaseModel
{
    
    protected $table = "provider_response_mappings";
    
    const SERVICE_PROVIDER_ASSIGNMENT_ID = "service_provider_assignment_id";
    const RESPONSE_FIELD = "response_field";
    const TARGET_FIELD = "target_field";
    const DATA_TYPE = "data_type";
    const PATH = "path";
    const TRANSFORM_FUNCTION = "transform_function";
    const IS_VERIFICATION_RESULT = "is_verification_result";
    const IS_REQUIRED = "is_required";
    protected $fillable = [
        self::SERVICE_PROVIDER_ASSIGNMENT_ID,
        self::RESPONSE_FIELD,
        self::TARGET_FIELD,
        self::DATA_TYPE,
        self::PATH,
        self::TRANSFORM_FUNCTION,
        self::IS_VERIFICATION_RESULT,
        self::IS_REQUIRED,
    ];
}