<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientServicePricing extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_service_pricing";
    
    const CLIENT_ID = "client_id";
    const SERVICE_ID = "service_id";
    const CUSTOM_PRICE = "custom_price";
    const EFFECTIVE_FROM = "effective_from";
    const EFFECTIVE_TO = "effective_to";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::SERVICE_ID,
        self::CUSTOM_PRICE,
        self::EFFECTIVE_FROM,
        self::EFFECTIVE_TO,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}
