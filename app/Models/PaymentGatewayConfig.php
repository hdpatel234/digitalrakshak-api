<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGatewayConfig extends BaseModel
{
    
    protected $table = "payment_gateway_configs";
    
    const GATEWAY_ID = "gateway_id";
    const CONFIG_NAME = "config_name";
    const ENVIRONMENT = "environment";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const API_TOKEN = "api_token";
    const MERCHANT_ID = "merchant_id";
    const MERCHANT_KEY = "merchant_key";
    const SALT = "salt";
    const BASE_URL = "base_url";
    const WEBHOOK_URL = "webhook_url";
    const CALLBACK_URL = "callback_url";
    const REDIRECT_URL = "redirect_url";
    const ENABLED_METHODS = "enabled_methods";
    const CURRENCIES = "currencies";
    const MIN_AMOUNT = "min_amount";
    const MAX_AMOUNT = "max_amount";
    const TRANSACTION_FEE_TYPE = "transaction_fee_type";
    const TRANSACTION_FEE_FIXED = "transaction_fee_fixed";
    const TRANSACTION_FEE_PERCENTAGE = "transaction_fee_percentage";
    const SETUP_FEE = "setup_fee";
    const ANNUAL_FEE = "annual_fee";
    const SETTLEMENT_CYCLE = "settlement_cycle";
    const SETTLEMENT_BANK = "settlement_bank";
    const SETTLEMENT_ACCOUNT = "settlement_account";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const IS_SANDBOX = "is_sandbox";
    const LAST_USED_AT = "last_used_at";
    const LAST_CHECKED_AT = "last_checked_at";
    const HEALTH_STATUS = "health_status";
    const ERROR_COUNT = "error_count";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::GATEWAY_ID,
        self::CONFIG_NAME,
        self::ENVIRONMENT,
        self::API_KEY,
        self::API_SECRET,
        self::API_TOKEN,
        self::MERCHANT_ID,
        self::MERCHANT_KEY,
        self::SALT,
        self::BASE_URL,
        self::WEBHOOK_URL,
        self::CALLBACK_URL,
        self::REDIRECT_URL,
        self::ENABLED_METHODS,
        self::CURRENCIES,
        self::MIN_AMOUNT,
        self::MAX_AMOUNT,
        self::TRANSACTION_FEE_TYPE,
        self::TRANSACTION_FEE_FIXED,
        self::TRANSACTION_FEE_PERCENTAGE,
        self::SETUP_FEE,
        self::ANNUAL_FEE,
        self::SETTLEMENT_CYCLE,
        self::SETTLEMENT_BANK,
        self::SETTLEMENT_ACCOUNT,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::IS_SANDBOX,
        self::LAST_USED_AT,
        self::LAST_CHECKED_AT,
        self::HEALTH_STATUS,
        self::ERROR_COUNT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}