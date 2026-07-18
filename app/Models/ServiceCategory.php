<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "service_categories";
    
    const CATEGORY_NAME = "category_name";
    const CATEGORY_CODE = "category_code";
    const CATEGORY_SLUG = "category_slug";
    const DESCRIPTION = "description";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CATEGORY_NAME,
        self::CATEGORY_CODE,
        self::CATEGORY_SLUG,
        self::DESCRIPTION,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}