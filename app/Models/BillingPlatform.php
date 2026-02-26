<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BillingPlatform extends BaseModel
{
    
    protected $table = "billing_platforms";
    
    const PLATFORM_NAME = "platform_name";
    const PLATFORM_CODE = "platform_code";
    const DESCRIPTION = "description";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::PLATFORM_NAME,
        self::PLATFORM_CODE,
        self::DESCRIPTION,
        self::IS_ACTIVE,
    ];
}