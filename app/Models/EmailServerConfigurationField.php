<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailServerConfigurationField extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "email_server_configuration_fields";
    
    const SERVER_TYPE_ID = "server_type_id";
    const FIELD_NAME = "field_name";
    const FIELD_LABEL = "field_label";
    const FIELD_TYPE = "field_type";
    const IS_REQUIRED = "is_required";
    const DEFAULT_VALUE = "default_value";
    const OPTIONS = "options";
    const SORT_ORDER = "sort_order";
    const HELP_TEXT = "help_text";
    const PLACEHOLDER = "placeholder";
    const VALIDATION_RULES = "validation_rules";
    const IS_ENCRYPTED = "is_encrypted";
    protected $fillable = [
        self::SERVER_TYPE_ID,
        self::FIELD_NAME,
        self::FIELD_LABEL,
        self::FIELD_TYPE,
        self::IS_REQUIRED,
        self::DEFAULT_VALUE,
        self::OPTIONS,
        self::SORT_ORDER,
        self::HELP_TEXT,
        self::PLACEHOLDER,
        self::VALIDATION_RULES,
        self::IS_ENCRYPTED,
    ];
}
