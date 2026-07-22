<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends BaseModel
{
    use SoftDeletes;
    protected $table = "packages";
    const PACKAGE_NAME = "package_name";
    const ICON = "icon";
    const PACKAGE_CODE = "package_code";
    const DESCRIPTION = "description";
    const TYPE = "type";
    const CLIENT_ID = "client_id";
    const TOTAL_PRICE = "total_price";
    const DISCOUNT_TYPE = "discount_type";
    const DISCOUNT_VALUE = "discount_value";
    const FINAL_PRICE = "final_price";
    protected $fillable = [
        self::PACKAGE_NAME,
        self::ICON,
        self::PACKAGE_CODE,
        self::DESCRIPTION,
        self::TYPE,
        self::CLIENT_ID,
        self::TOTAL_PRICE,
        self::DISCOUNT_TYPE,
        self::DISCOUNT_VALUE,
        self::FINAL_PRICE,
        self::STATUS
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, self::CLIENT_ID);
    }
}
