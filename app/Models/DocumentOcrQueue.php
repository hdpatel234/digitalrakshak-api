<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentOcrQueue extends BaseModel
{
    
    protected $table = "document_ocr_queue";
    
    const DOCUMENT_ID = "document_id";
    const PRIORITY = "priority";
    const STATUS = "status";
    const ATTEMPTS = "attempts";
    const MAX_ATTEMPTS = "max_attempts";
    const OCR_TEXT = "ocr_text";
    const ERROR_MESSAGE = "error_message";
    const PROCESSED_AT = "processed_at";
    const COMPLETED_AT = "completed_at";
    protected $fillable = [
        self::DOCUMENT_ID,
        self::PRIORITY,
        self::STATUS,
        self::ATTEMPTS,
        self::MAX_ATTEMPTS,
        self::OCR_TEXT,
        self::ERROR_MESSAGE,
        self::PROCESSED_AT,
        self::COMPLETED_AT,
    ];
}