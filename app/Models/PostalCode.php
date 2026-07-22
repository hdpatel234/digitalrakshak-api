<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PostalCode extends BaseModel
{
    use SoftDeletes;
    protected $table = "postal_codes";
    const COUNTRY_ID = "country_id";
    const STATE_ID = "state_id";
    const CITY_ID = "city_id";
    const POSTAL_CODE = "postal_code";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const ACCURACY = "accuracy";
    protected $fillable = [
        self::COUNTRY_ID,
        self::STATE_ID,
        self::CITY_ID,
        self::POSTAL_CODE,
        self::LATITUDE,
        self::LONGITUDE,
        self::ACCURACY,
        self::STATUS,
    ];
}
