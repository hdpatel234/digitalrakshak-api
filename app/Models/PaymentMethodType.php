<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethodType extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "payment_method_types";
    
    const METHOD_NAME = "method_name";
    const METHOD_CODE = "method_code";
    const CATEGORY = "category";
    const ICON = "icon";
    const DESCRIPTION = "description";
    const CONFIGURATION_SCHEMA = "configuration_schema";
    const IS_ACTIVE = "is_active";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::METHOD_NAME,
        self::METHOD_CODE,
        self::CATEGORY,
        self::ICON,
        self::DESCRIPTION,
        self::CONFIGURATION_SCHEMA,
        self::IS_ACTIVE,
        self::DISPLAY_ORDER,
    ];
}