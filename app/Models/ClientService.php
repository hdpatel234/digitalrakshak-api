<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientService extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_services";
    
    const CLIENT_ID = "client_id";
    const SERVICE_ID = "service_id";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::SERVICE_ID,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}
