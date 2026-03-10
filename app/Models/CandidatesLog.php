<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CandidatesLog extends BaseModel
{
    
    protected $table = "candidates_logs";
    
    const CANDIDATE_ID = "candidate_id";
    const ACTION = "action";
    const IP_ADDRESS = "ip_address";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CANDIDATE_ID,
        self::ACTION,
        self::IP_ADDRESS,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}