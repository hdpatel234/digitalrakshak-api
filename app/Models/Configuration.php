<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
class Configuration extends BaseModel
{
    use SoftDeletes;

    protected $table = "configurations";

    const CONFIG_KEY = "config_key";
    const CONFIG_VALUE = "config_value";
    const DESCRIPTION = "description";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";

    protected $fillable = [
        self::CONFIG_KEY,
        self::CONFIG_VALUE,
        self::DESCRIPTION,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}
