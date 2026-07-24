<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateServiceData extends BaseModel
{
    use SoftDeletes;
    protected $table = "candidate_service_data";
    const order_item_id = "order_item_id";
    const FIELD_ID = "field_id";
    const FIELD_VALUE = "field_value";
    protected $fillable = [
        self::order_item_id,
        self::FIELD_ID,
        self::FIELD_VALUE,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function candidateService(): BelongsTo
    {
        return $this->belongsTo(CandidateService::class, self::order_item_id);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServicesField::class, self::FIELD_ID);
    }
}
