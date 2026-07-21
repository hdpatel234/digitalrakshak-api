<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SavedPaymentMethod extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "saved_payment_methods";
    
    const CLIENT_ID = "client_id";
    const USER_ID = "user_id";
    const CUSTOMER_ID = "customer_id";
    const GATEWAY_CONFIG_ID = "gateway_config_id";
    const METHOD_TYPE_ID = "method_type_id";
    const GATEWAY_CUSTOMER_ID = "gateway_customer_id";
    const GATEWAY_PAYMENT_METHOD_ID = "gateway_payment_method_id";
    const PAYMENT_TOKEN = "payment_token";
    const DISPLAY_NAME = "display_name";
    const MASKED_DETAILS = "masked_details";
    const EXPIRY_MONTH = "expiry_month";
    const EXPIRY_YEAR = "expiry_year";
    const CARD_HOLDER_NAME = "card_holder_name";
    const CARD_BRAND = "card_brand";
    const BANK_NAME = "bank_name";
    const UPI_ID = "upi_id";
    const IS_DEFAULT = "is_default";
    const IS_ACTIVE = "is_active";
    const LAST_USED_AT = "last_used_at";
    const USED_COUNT = "used_count";
    protected $fillable = [
        self::CLIENT_ID,
        self::USER_ID,
        self::CUSTOMER_ID,
        self::GATEWAY_CONFIG_ID,
        self::METHOD_TYPE_ID,
        self::GATEWAY_CUSTOMER_ID,
        self::GATEWAY_PAYMENT_METHOD_ID,
        self::PAYMENT_TOKEN,
        self::DISPLAY_NAME,
        self::MASKED_DETAILS,
        self::EXPIRY_MONTH,
        self::EXPIRY_YEAR,
        self::CARD_HOLDER_NAME,
        self::CARD_BRAND,
        self::BANK_NAME,
        self::UPI_ID,
        self::IS_DEFAULT,
        self::IS_ACTIVE,
        self::LAST_USED_AT,
        self::USED_COUNT,
    ];
}
