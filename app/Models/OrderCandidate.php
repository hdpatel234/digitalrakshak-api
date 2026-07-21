<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCandidate extends BaseModel
{
    use SoftDeletes;

    protected $table = "order_candidates";

    const ORDER_ID = "order_id";
    const CANDIDATE_ID = "candidate_id";
    const CANDIDATE_DATA = "candidate_data";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::ORDER_ID,
        self::CANDIDATE_ID,
        self::CANDIDATE_DATA,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, self::CANDIDATE_ID);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(CandidateOrder::class, self::ORDER_ID);
    }
}
