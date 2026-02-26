<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends BaseModel
{
    
    protected $table = "services";
    
    const SERVICE_NAME = "service_name";
    const SERVICE_CODE = "service_code";
    const DESCRIPTION = "description";
    const BASE_PRICE = "base_price";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::SERVICE_NAME,
        self::SERVICE_CODE,
        self::DESCRIPTION,
        self::BASE_PRICE,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}