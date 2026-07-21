<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LocationServicePricing extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "location_service_pricing";
    
    const CLIENT_ID = "client_id";
    const SERVICE_ID = "service_id";
    const COUNTRY_ID = "country_id";
    const STATE_ID = "state_id";
    const CITY_ID = "city_id";
    const PRICE_ADJUSTMENT_TYPE = "price_adjustment_type";
    const PRICE_ADJUSTMENT = "price_adjustment";
    const FINAL_PRICE = "final_price";
    const EFFECTIVE_FROM = "effective_from";
    const EFFECTIVE_TO = "effective_to";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::SERVICE_ID,
        self::COUNTRY_ID,
        self::STATE_ID,
        self::CITY_ID,
        self::PRICE_ADJUSTMENT_TYPE,
        self::PRICE_ADJUSTMENT,
        self::FINAL_PRICE,
        self::EFFECTIVE_FROM,
        self::EFFECTIVE_TO,
        self::IS_ACTIVE,
        self::CREATED_BY,
    ];
}
