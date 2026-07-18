<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConfigDefinition extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_config_definitions";
    
    const CATEGORY_ID = "category_id";
    const CONFIG_KEY = "config_key";
    const CONFIG_NAME = "config_name";
    const DESCRIPTION = "description";
    const VALUE_TYPE = "value_type";
    const DEFAULT_VALUE = "default_value";
    const POSSIBLE_VALUES = "possible_values";
    const VALIDATION_RULES = "validation_rules";
    const IS_REQUIRED = "is_required";
    const IS_EDITABLE = "is_editable";
    const IS_PRIVATE = "is_private";
    const DISPLAY_ORDER = "display_order";
    const UI_COMPONENT = "ui_component";
    const UI_PROPS = "ui_props";
    const DEPENDS_ON = "depends_on";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    protected $fillable = [
        self::CATEGORY_ID,
        self::CONFIG_KEY,
        self::CONFIG_NAME,
        self::DESCRIPTION,
        self::VALUE_TYPE,
        self::DEFAULT_VALUE,
        self::POSSIBLE_VALUES,
        self::VALIDATION_RULES,
        self::IS_REQUIRED,
        self::IS_EDITABLE,
        self::IS_PRIVATE,
        self::DISPLAY_ORDER,
        self::UI_COMPONENT,
        self::UI_PROPS,
        self::DEPENDS_ON,
        self::IS_ACTIVE,
        self::CREATED_BY,
    ];

    protected function casts(): array
    {
        return [
            self::POSSIBLE_VALUES => 'array',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(UserConfigValue::class, UserConfigValue::CONFIG_ID);
    }
}
