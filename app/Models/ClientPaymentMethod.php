<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPaymentMethod extends BaseModel
{
    
    protected $table = "client_payment_methods";
    
    const CLIENT_ID = "client_id";
    const METHOD_TYPE_ID = "method_type_id";
    const GATEWAY_CONFIG_ID = "gateway_config_id";
    const DISPLAY_NAME = "display_name";
    const DESCRIPTION = "description";
    const ICON = "icon";
    const DISPLAY_ORDER = "display_order";
    const IS_ENABLED = "is_enabled";
    const IS_DEFAULT = "is_default";
    const MIN_AMOUNT = "min_amount";
    const MAX_AMOUNT = "max_amount";
    const INSTRUCTIONS = "instructions";
    protected $fillable = [
        self::CLIENT_ID,
        self::METHOD_TYPE_ID,
        self::GATEWAY_CONFIG_ID,
        self::DISPLAY_NAME,
        self::DESCRIPTION,
        self::ICON,
        self::DISPLAY_ORDER,
        self::IS_ENABLED,
        self::IS_DEFAULT,
        self::MIN_AMOUNT,
        self::MAX_AMOUNT,
        self::INSTRUCTIONS,
    ];
}