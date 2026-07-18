<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateManager extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "candidate_managers";
    
    const CANDIDATE_ID = "candidate_id";
    const EMAIL = "email";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CANDIDATE_ID,
        self::EMAIL,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}