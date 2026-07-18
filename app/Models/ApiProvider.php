<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ApiProvider extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "api_providers";
    
    const NAME = "name";
    const CODE = "code";
    const PROVIDER_CLASS = "class";
    const CONFIG_FIELDS = "config_fields";
    const CREDENTIALS = "credentials";
    const SETTINGS = "settings";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const PRIORITY = "priority";
    protected $fillable = [
        self::NAME,
        self::CODE,
        self::PROVIDER_CLASS,
        self::CONFIG_FIELDS,
        self::CREDENTIALS,
        self::SETTINGS,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::PRIORITY,
    ];
}