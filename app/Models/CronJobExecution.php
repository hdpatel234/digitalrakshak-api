<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronJobExecution extends Model
{
    protected $fillable = [
        'job_key',
        'started_at',
        'completed_at',
        'status',
        'triggered_by',
        'triggered_by_user_id',
        'ip_address',
        'duration_seconds',
        'output',
        'error_message',
        'error_stack',
        'processed_count',
        'success_count',
        'failed_count',
        'processed_logs',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(CronJob::class, 'job_key', 'job_key');
    }
}
