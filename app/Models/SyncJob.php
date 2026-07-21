<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SyncJob extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "sync_jobs";
    
    const JOB_TYPE = "job_type";
    const CLIENT_ID = "client_id";
    const CONFIG_ID = "config_id";
    const STATUS = "status";
    const ITEMS_PROCESSED = "items_processed";
    const ITEMS_FAILED = "items_failed";
    const STARTED_AT = "started_at";
    const COMPLETED_AT = "completed_at";
    const ERROR_MESSAGE = "error_message";
    const SYNC_LOG = "sync_log";
    protected $fillable = [
        self::JOB_TYPE,
        self::CLIENT_ID,
        self::CONFIG_ID,
        self::STATUS,
        self::ITEMS_PROCESSED,
        self::ITEMS_FAILED,
        self::STARTED_AT,
        self::COMPLETED_AT,
        self::ERROR_MESSAGE,
        self::SYNC_LOG,
    ];
}
