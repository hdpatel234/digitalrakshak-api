<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class State extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "states";
    
    const COUNTRY_ID = "country_id";
    const NAME = "name";
    const CODE = "code";
    const TYPE = "type";
    const CAPITAL = "capital";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const AREA_KM2 = "area_km2";
    const POPULATION = "population";
    const IS_ACTIVE = "is_active";
    const DISPLAY_ORDER = "display_order";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::COUNTRY_ID,
        self::NAME,
        self::CODE,
        self::TYPE,
        self::CAPITAL,
        self::LATITUDE,
        self::LONGITUDE,
        self::AREA_KM2,
        self::POPULATION,
        self::IS_ACTIVE,
        self::DISPLAY_ORDER,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}