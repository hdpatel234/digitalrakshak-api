<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends BaseModel
{
    
    protected $table = "payment_gateways";
    
    const GATEWAY_NAME = "gateway_name";
    const GATEWAY_CODE = "gateway_code";
    const PROVIDER_COMPANY = "provider_company";
    const WEBSITE = "website";
    const DESCRIPTION = "description";
    const LOGO = "logo";
    const SUPPORTED_METHODS = "supported_methods";
    const CONFIGURATION_SCHEMA = "configuration_schema";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::GATEWAY_NAME,
        self::GATEWAY_CODE,
        self::PROVIDER_COMPANY,
        self::WEBSITE,
        self::DESCRIPTION,
        self::LOGO,
        self::SUPPORTED_METHODS,
        self::CONFIGURATION_SCHEMA,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::DISPLAY_ORDER,
    ];
}