<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SupportPriority extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "support_priorities";
    
    const NAME = "name";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::NAME,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}