<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends BaseModel
{
    use SoftDeletes;
    
    protected $table = "services";
    
    const SERVICE_CATEGORY = "service_category";
    const SERVICE_TYPE = "service_type";
    const SERVICE_NAME = "service_name";
    const SERVICE_CODE = "service_code";
    const DESCRIPTION = "description";
    const ICON = "icon";
    const BASE_PRICE = "base_price";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::SERVICE_CATEGORY,
        self::SERVICE_TYPE,
        self::SERVICE_NAME,
        self::SERVICE_CODE,
        self::DESCRIPTION,
        self::ICON,
        self::BASE_PRICE,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, self::SERVICE_CATEGORY);
    }

    protected $casts = [
        self::SERVICE_TYPE => \App\Enums\ServiceType::class,
    ];
}
