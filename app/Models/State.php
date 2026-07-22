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
    const DISPLAY_ORDER = "display_order";
    const CITIES_SYNCED_AT = "cities_synced_at";
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
        self::DISPLAY_ORDER,
        self::CITIES_SYNCED_AT,
        self::STATUS,
    ];
}
