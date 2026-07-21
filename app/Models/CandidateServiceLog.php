<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateServiceLog extends Model
{
    use SoftDeletes, HasFactory;

    public const CANDIDATE_ID = 'candidate_id';
    public const order_item_id = 'order_item_id';
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const STATUS = 'status';

    protected $fillable = [
        self::CANDIDATE_ID,
        self::order_item_id,
        self::TITLE,
        self::DESCRIPTION,
        self::STATUS,
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function candidateService(): BelongsTo
    {
        return $this->belongsTo(CandidateService::class);
    }
}
