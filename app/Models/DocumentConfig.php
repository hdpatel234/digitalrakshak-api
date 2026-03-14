<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentConfig extends BaseModel
{
    protected $table = "client_document_configs";

    const DOCUMENT_PLATFORM_ID = "document_platform_id";
    const CONFIG_NAME = "config_name";
    const IS_DEFAULT = "is_default";
    const API_URL = "api_url";
    const USERNAME = "username";
    const PASSWORD = "password";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const ACCESS_TOKEN = "access_token";
    const REFRESH_TOKEN = "refresh_token";
    const TOKEN_EXPIRES_AT = "token_expires_at";
    const ROOT_FOLDER = "root_folder";
    const CLIENT_FOLDER = "client_folder";
    const FOLDER_STRUCTURE = "folder_structure";
    const FILE_NAMING_CONVENTION = "file_naming_convention";
    const MAX_FILE_SIZE = "max_file_size";
    const ALLOWED_FILE_TYPES = "allowed_file_types";
    const IS_PUBLIC_READABLE = "is_public_readable";
    const SHARE_EXPIRY_DAYS = "share_expiry_days";
    const WEBHOOK_SECRET = "webhook_secret";
    const ADDITIONAL_CONFIG = "additional_config";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::DOCUMENT_PLATFORM_ID,
        self::CONFIG_NAME,
        self::IS_DEFAULT,
        self::API_URL,
        self::USERNAME,
        self::PASSWORD,
        self::API_KEY,
        self::API_SECRET,
        self::ACCESS_TOKEN,
        self::REFRESH_TOKEN,
        self::TOKEN_EXPIRES_AT,
        self::ROOT_FOLDER,
        self::CLIENT_FOLDER,
        self::FOLDER_STRUCTURE,
        self::FILE_NAMING_CONVENTION,
        self::MAX_FILE_SIZE,
        self::ALLOWED_FILE_TYPES,
        self::IS_PUBLIC_READABLE,
        self::SHARE_EXPIRY_DAYS,
        self::WEBHOOK_SECRET,
        self::ADDITIONAL_CONFIG,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    protected $casts = [
        self::ADDITIONAL_CONFIG => 'array',
        self::ALLOWED_FILE_TYPES => 'array',
        self::IS_DEFAULT => 'boolean',
    ];

    public function documentPlatform(): BelongsTo
    {
        return $this->belongsTo(DocumentPlatform::class, self::DOCUMENT_PLATFORM_ID);
    }
}
