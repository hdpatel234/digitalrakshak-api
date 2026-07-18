<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailLog extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "email_logs";
    protected $casts = [
        self::PROVIDER_RESPONSE => 'array',
        self::METADATA => 'array',
    ];
    
    const EMAIL_QUEUE_ID = "email_queue_id";
    const SERVER_ID = "server_id";
    const STATUS = "status";
    const PROVIDER_RESPONSE = "provider_response";
    const ERROR_MESSAGE = "error_message";
    const METADATA = "metadata";
    
    protected $fillable = [
        self::EMAIL_QUEUE_ID,
        self::SERVER_ID,
        self::STATUS,
        self::PROVIDER_RESPONSE,
        self::ERROR_MESSAGE,
        self::METADATA,
    ];

    public function emailQueue()
    {
        return $this->belongsTo(EmailQueue::class, 'email_queue_id');
    }
}
