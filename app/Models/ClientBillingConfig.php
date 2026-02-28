<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientBillingConfig extends BaseModel
{
    
    protected $table = "client_billing_configs";
    
    const CLIENT_ID = "client_id";
    const BILLING_PLATFORM_ID = "billing_platform_id";
    const CONFIG_NAME = "config_name";
    const IS_DEFAULT = "is_default";
    const API_URL = "api_url";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const API_TOKEN = "api_token";
    const WEBHOOK_SECRET = "webhook_secret";
    const ADDITIONAL_CONFIG = "additional_config";
    const INVOICE_PREFIX = "invoice_prefix";
    const INVOICE_SERIES = "invoice_series";
    const TAX_RATE = "tax_rate";
    const CURRENCY = "currency";
    const PAYMENT_TERMS_DAYS = "payment_terms_days";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::BILLING_PLATFORM_ID,
        self::CONFIG_NAME,
        self::IS_DEFAULT,
        self::API_URL,
        self::API_KEY,
        self::API_SECRET,
        self::API_TOKEN,
        self::WEBHOOK_SECRET,
        self::ADDITIONAL_CONFIG,
        self::INVOICE_PREFIX,
        self::INVOICE_SERIES,
        self::TAX_RATE,
        self::CURRENCY,
        self::PAYMENT_TERMS_DAYS,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    protected $casts = [
        self::ADDITIONAL_CONFIG => 'array',
        self::IS_DEFAULT => 'boolean',
    ];

    public function billingPlatform(): BelongsTo
    {
        return $this->belongsTo(BillingPlatform::class, self::BILLING_PLATFORM_ID);
    }
}
