<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDependency extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "service_dependencies";
    
    const SERVICE_ID = "service_id";
    const DEPENDS_ON_SERVICE_ID = "depends_on_service_id";
    const DEPENDENCY_TYPE = "dependency_type";
    protected $fillable = [
        self::SERVICE_ID,
        self::DEPENDS_ON_SERVICE_ID,
        self::DEPENDENCY_TYPE,
    ];
}