<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderCost extends BaseModel
{
    
    protected $table = "provider_costs";
    
    const PROVIDER_ID = "provider_id";
    const SERVICE_ID = "service_id";
    const COST_PER_CALL = "cost_per_call";
    const CURRENCY = "currency";
    const BILLING_MODEL = "billing_model";
    const MINIMUM_COMMITMENT = "minimum_commitment";
    const COMMITMENT_PERIOD = "commitment_period";
    const EFFECTIVE_FROM = "effective_from";
    const EFFECTIVE_TO = "effective_to";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::PROVIDER_ID,
        self::SERVICE_ID,
        self::COST_PER_CALL,
        self::CURRENCY,
        self::BILLING_MODEL,
        self::MINIMUM_COMMITMENT,
        self::COMMITMENT_PERIOD,
        self::EFFECTIVE_FROM,
        self::EFFECTIVE_TO,
        self::IS_ACTIVE,
    ];
}