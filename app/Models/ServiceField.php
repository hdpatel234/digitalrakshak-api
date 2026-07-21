<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceField extends BaseModel
{
    use SoftDeletes;

    protected $table = "services_fields";

    const SERVICE_ID = "service_id";
    const FIELD_NAME = "field_name";
    const SECTION = "section";
    const FIELD_LABEL = "field_label";
    const FIELD_TYPE = "field_type";
    const IS_REQUIRED = "is_required";
    const IS_HIDDEN = "is_hidden";
    const OR_GROUP_NAME = "or_group_name";
    const VALIDATION_REGEX = "validation_regex";
    const DISPLAY_ORDER = "display_order";
    const FIELD_OPTIONS = "field_options";
    const IS_VERIFIABLE = "is_verifiable";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";

    protected $fillable = [
        self::SERVICE_ID,
        self::FIELD_NAME,
        self::SECTION,
        self::FIELD_LABEL,
        self::FIELD_TYPE,
        self::IS_REQUIRED,
        self::IS_HIDDEN,
        self::OR_GROUP_NAME,
        self::VALIDATION_REGEX,
        self::DISPLAY_ORDER,
        self::FIELD_OPTIONS,
        self::IS_VERIFIABLE,
        self::IS_ACTIVE,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, self::SERVICE_ID);
    }
}
