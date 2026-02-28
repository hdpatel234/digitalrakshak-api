<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends BaseModel
{
    
    protected $table = "countries";
    
    const NAME = "name";
    const ISO_CODE_2 = "iso_code_2";
    const ISO_CODE_3 = "iso_code_3";
    const NUMERIC_CODE = "numeric_code";
    const PHONE_CODE = "phone_code";
    const CURRENCY_CODE = "currency_code";
    const CURRENCY_SYMBOL = "currency_symbol";
    const CAPITAL = "capital";
    const CONTINENT = "continent";
    const FLAG_ICON = "flag_icon";
    const FLAG_IMAGE = "flag_image";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const TIMEZONES = "timezones";
    const POSTAL_CODE_FORMAT = "postal_code_format";
    const POSTAL_CODE_REGEX = "postal_code_regex";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const DISPLAY_ORDER = "display_order";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::NAME,
        self::ISO_CODE_2,
        self::ISO_CODE_3,
        self::NUMERIC_CODE,
        self::PHONE_CODE,
        self::CURRENCY_CODE,
        self::CURRENCY_SYMBOL,
        self::CAPITAL,
        self::CONTINENT,
        self::FLAG_ICON,
        self::FLAG_IMAGE,
        self::LATITUDE,
        self::LONGITUDE,
        self::TIMEZONES,
        self::POSTAL_CODE_FORMAT,
        self::POSTAL_CODE_REGEX,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::DISPLAY_ORDER,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}