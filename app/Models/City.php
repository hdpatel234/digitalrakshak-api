<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class City extends BaseModel
{
    use SoftDeletes;
    protected $table = "cities";
    const STATE_ID = "state_id";
    const COUNTRY_ID = "country_id";
    const NAME = "name";
    const LOCAL_NAME = "local_name";
    const DISTRICT = "district";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const POSTAL_CODE = "postal_code";
    const POSTAL_CODES = "postal_codes";
    const TIMEZONE = "timezone";
    const IS_CAPITAL = "is_capital";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::STATE_ID,
        self::COUNTRY_ID,
        self::NAME,
        self::LOCAL_NAME,
        self::DISTRICT,
        self::LATITUDE,
        self::LONGITUDE,
        self::POSTAL_CODE,
        self::POSTAL_CODES,
        self::TIMEZONE,
        self::IS_CAPITAL,
        self::DISPLAY_ORDER,
        self::STATUS
    ];
}
