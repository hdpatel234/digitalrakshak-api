<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentVerification extends Model
{
    protected $fillable = [
        'candidate_service_id',
        'token',
        'company_email',
        'candidate_data',
        'status',
        'remarks',
        'verified_at',
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
