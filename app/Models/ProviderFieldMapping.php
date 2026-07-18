<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderFieldMapping extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "provider_field_mappings";
    
    const SERVICE_PROVIDER_ASSIGNMENT_ID = "service_provider_assignment_id";
    const SERVICE_FIELD_ID = "service_field_id";
    const PROVIDER_FIELD_NAME = "provider_field_name";
    const FIELD_PATH = "field_path";
    const TRANSFORM_FUNCTION = "transform_function";
    const IS_REQUIRED = "is_required";
    const DEFAULT_VALUE = "default_value";
    protected $fillable = [
        self::SERVICE_PROVIDER_ASSIGNMENT_ID,
        self::SERVICE_FIELD_ID,
        self::PROVIDER_FIELD_NAME,
        self::FIELD_PATH,
        self::TRANSFORM_FUNCTION,
        self::IS_REQUIRED,
        self::DEFAULT_VALUE,
    ];
}