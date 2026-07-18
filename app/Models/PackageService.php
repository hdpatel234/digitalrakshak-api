<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PackageService extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "package_services";
    
    const PACKAGE_ID = "package_id";
    const SERVICE_ID = "service_id";
    const PRICE_OVERRIDE = "price_override";
    const IS_MANDATORY = "is_mandatory";
    const DISPLAY_ORDER = "display_order";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::PACKAGE_ID,
        self::SERVICE_ID,
        self::PRICE_OVERRIDE,
        self::IS_MANDATORY,
        self::DISPLAY_ORDER,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}