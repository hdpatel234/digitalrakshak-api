<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentVersion extends BaseModel
{
    
    protected $table = "document_versions";
    
    const DOCUMENT_ID = "document_id";
    const VERSION_NUMBER = "version_number";
    const STORED_FILENAME = "stored_filename";
    const FILE_PATH = "file_path";
    const FILE_SIZE = "file_size";
    const FILE_HASH = "file_hash";
    const EXTERNAL_FILE_ID = "external_file_id";
    const CHANGE_REASON = "change_reason";
    const CREATED_BY = "created_by";
    protected $fillable = [
        self::DOCUMENT_ID,
        self::VERSION_NUMBER,
        self::STORED_FILENAME,
        self::FILE_PATH,
        self::FILE_SIZE,
        self::FILE_HASH,
        self::EXTERNAL_FILE_ID,
        self::CHANGE_REASON,
        self::CREATED_BY,
    ];
}