<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailAttachment extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "email_attachments";
    
    const EMAIL_QUEUE_ID = "email_queue_id";
    const DOCUMENT_ID = "document_id";
    const FILENAME = "filename";
    const FILE_PATH = "file_path";
    const FILE_SIZE = "file_size";
    const MIME_TYPE = "mime_type";
    const CID = "cid";
    const IS_INLINE = "is_inline";
    protected $fillable = [
        self::EMAIL_QUEUE_ID,
        self::DOCUMENT_ID,
        self::FILENAME,
        self::FILE_PATH,
        self::FILE_SIZE,
        self::MIME_TYPE,
        self::CID,
        self::IS_INLINE,
    ];
}