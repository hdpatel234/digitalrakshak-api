<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EmploymentVerification extends Model
{
    use SoftDeletes;

    const order_item_id = 'order_item_id';
    const TOKEN = 'token';
    const COMPANY_EMAIL = 'company_email';
    const CANDIDATE_DATA = 'candidate_data';
    const STATUS = 'status';
    const REMARKS = 'remarks';
    const VERIFIED_AT = 'verified_at';

    protected $fillable = [
        self::order_item_id,
        self::TOKEN,
        self::COMPANY_EMAIL,
        self::CANDIDATE_DATA,
        self::STATUS,
        self::REMARKS,
        self::VERIFIED_AT,
    ];

    protected $casts = [
        'candidate_data' => 'array',
        'verified_at' => 'datetime',
    ];

    public function candidateService()
    {
        return $this->belongsTo(CandidateService::class);
    }
}
