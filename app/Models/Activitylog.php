<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Activitylog extends BaseModel
{
    use SoftDeletes;

    protected $table = "activity_log";

    const DESCRIPTION = "description";
    const USER_ID = "user_id";
    const DATE = "date";
    const IP_ADDRESS = "ip_address";
    protected $fillable = [
        self::DESCRIPTION,
        self::USER_ID,
        self::DATE,
        self::IP_ADDRESS,
    ];
}
