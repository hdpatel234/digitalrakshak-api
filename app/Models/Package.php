<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "packages";
    
    const PACKAGE_NAME = "package_name";
    const PACKAGE_CODE = "package_code";
    const DESCRIPTION = "description";
    const TYPE = "type";
    const CLIENT_ID = "client_id";
    const TOTAL_PRICE = "total_price";
    const DISCOUNT_TYPE = "discount_type";
    const DISCOUNT_VALUE = "discount_value";
    const FINAL_PRICE = "final_price";
    const IS_ACTIVE = "is_active";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    const ICON = "icon";
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
        self::IS_ACTIVE,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, self::CLIENT_ID);
    }
}
