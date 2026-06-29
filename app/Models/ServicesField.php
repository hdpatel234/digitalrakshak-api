<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServicesField extends BaseModel
{
    
    protected $table = "services_fields";
    
    const SERVICE_ID = "service_id";
    const FIELD_NAME = "field_name";
    const FIELD_LABEL = "field_label";
    const FIELD_TYPE = "field_type";
    const IS_REQUIRED = "is_required";
    const VALIDATION_REGEX = "validation_regex";
    const DISPLAY_ORDER = "display_order";
    const STATUS = "status";
    const SECTION = "section";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::SERVICE_ID,
        self::FIELD_NAME,
        self::FIELD_LABEL,
        self::SECTION,
        self::FIELD_TYPE,
        self::IS_REQUIRED,
        self::VALIDATION_REGEX,
        self::DISPLAY_ORDER,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}