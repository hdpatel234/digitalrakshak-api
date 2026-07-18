<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientServiceArea extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_service_areas";
    
    const CLIENT_ID = "client_id";
    const COUNTRY_ID = "country_id";
    const STATE_ID = "state_id";
    const CITY_ID = "city_id";
    const SERVICE_TYPE = "service_type";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::COUNTRY_ID,
        self::STATE_ID,
        self::CITY_ID,
        self::SERVICE_TYPE,
        self::IS_ACTIVE,
        self::CREATED_BY,
    ];
}