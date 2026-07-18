<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPaymentGateway extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_payment_gateways";
    
    const CLIENT_ID = "client_id";
    const GATEWAY_CONFIG_ID = "gateway_config_id";
    const DISPLAY_NAME = "display_name";
    const DISPLAY_ORDER = "display_order";
    const ENABLED_METHODS = "enabled_methods";
    const CURRENCIES = "currencies";
    const FEE_TYPE = "fee_type";
    const FEE_FIXED = "fee_fixed";
    const FEE_PERCENTAGE = "fee_percentage";
    const MIN_AMOUNT = "min_amount";
    const MAX_AMOUNT = "max_amount";
    const DAILY_LIMIT = "daily_limit";
    const MONTHLY_LIMIT = "monthly_limit";
    const IS_ENABLED = "is_enabled";
    const IS_DEFAULT = "is_default";
    const IS_MANDATORY = "is_mandatory";
    const TOTAL_TRANSACTIONS = "total_transactions";
    const TOTAL_AMOUNT = "total_amount";
    const LAST_TRANSACTION_AT = "last_transaction_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::GATEWAY_CONFIG_ID,
        self::DISPLAY_NAME,
        self::DISPLAY_ORDER,
        self::ENABLED_METHODS,
        self::CURRENCIES,
        self::FEE_TYPE,
        self::FEE_FIXED,
        self::FEE_PERCENTAGE,
        self::MIN_AMOUNT,
        self::MAX_AMOUNT,
        self::DAILY_LIMIT,
        self::MONTHLY_LIMIT,
        self::IS_ENABLED,
        self::IS_DEFAULT,
        self::IS_MANDATORY,
        self::TOTAL_TRANSACTIONS,
        self::TOTAL_AMOUNT,
        self::LAST_TRANSACTION_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    public function gatewayConfig(): BelongsTo
    {
        return $this->belongsTo(PaymentGatewayConfig::class, self::GATEWAY_CONFIG_ID);
    }
}
