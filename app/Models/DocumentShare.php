<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentShare extends BaseModel
{
    
    protected $table = "document_shares";
    
    const DOCUMENT_ID = "document_id";
    const SHARE_TOKEN = "share_token";
    const SHARE_TYPE = "share_type";
    const PASSWORD = "password";
    const EXPIRES_AT = "expires_at";
    const MAX_DOWNLOADS = "max_downloads";
    const DOWNLOAD_COUNT = "download_count";
    const SHARED_WITH_EMAIL = "shared_with_email";
    const SHARED_WITH_NAME = "shared_with_name";
    const ACCESS_PERMISSION = "access_permission";
    const CREATED_BY = "created_by";
    const LAST_ACCESSED_AT = "last_accessed_at";
    protected $fillable = [
        self::DOCUMENT_ID,
        self::SHARE_TOKEN,
        self::SHARE_TYPE,
        self::PASSWORD,
        self::EXPIRES_AT,
        self::MAX_DOWNLOADS,
        self::DOWNLOAD_COUNT,
        self::SHARED_WITH_EMAIL,
        self::SHARED_WITH_NAME,
        self::ACCESS_PERMISSION,
        self::CREATED_BY,
        self::LAST_ACCESSED_AT,
    ];
}