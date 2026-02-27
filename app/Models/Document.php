<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends BaseModel
{
    
    protected $table = "documents";
    
    const CLIENT_ID = "client_id";
    const DOCUMENT_CONFIG_ID = "document_config_id";
    const CANDIDATE_ID = "candidate_id";
    const ORDER_ID = "order_id";
    const ORDER_ITEM_ID = "order_item_id";
    const INVITATION_ID = "invitation_id";
    const TICKET_ID = "ticket_id";
    const DOCUMENT_TYPE = "document_type";
    const DOCUMENT_CATEGORY = "document_category";
    const ORIGINAL_FILENAME = "original_filename";
    const STORED_FILENAME = "stored_filename";
    const FILE_PATH = "file_path";
    const FILE_SIZE = "file_size";
    const FILE_HASH = "file_hash";
    const MIME_TYPE = "mime_type";
    const EXTENSION = "extension";
    const EXTERNAL_FILE_ID = "external_file_id";
    const EXTERNAL_SHARE_LINK = "external_share_link";
    const EXTERNAL_SHARE_ID = "external_share_id";
    const SHARE_PASSWORD = "share_password";
    const SHARE_EXPIRES_AT = "share_expires_at";
    const VERSION = "version";
    const IS_ENCRYPTED = "is_encrypted";
    const ENCRYPTION_KEY = "encryption_key";
    const METADATA = "metadata";
    const OCR_TEXT = "ocr_text";
    const OCR_STATUS = "ocr_status";
    const OCR_COMPLETED_AT = "ocr_completed_at";
    const THUMBNAIL_URL = "thumbnail_url";
    const PREVIEW_URL = "preview_url";
    const DOWNLOAD_COUNT = "download_count";
    const LAST_DOWNLOADED_AT = "last_downloaded_at";
    const LAST_DOWNLOADED_BY = "last_downloaded_by";
    const STATUS = "status";
    const SYNC_STATUS = "sync_status";
    const SYNC_MESSAGE = "sync_message";
    const LAST_SYNC_AT = "last_sync_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::DOCUMENT_CONFIG_ID,
        self::CANDIDATE_ID,
        self::ORDER_ID,
        self::ORDER_ITEM_ID,
        self::INVITATION_ID,
        self::TICKET_ID,
        self::DOCUMENT_TYPE,
        self::DOCUMENT_CATEGORY,
        self::ORIGINAL_FILENAME,
        self::STORED_FILENAME,
        self::FILE_PATH,
        self::FILE_SIZE,
        self::FILE_HASH,
        self::MIME_TYPE,
        self::EXTENSION,
        self::EXTERNAL_FILE_ID,
        self::EXTERNAL_SHARE_LINK,
        self::EXTERNAL_SHARE_ID,
        self::SHARE_PASSWORD,
        self::SHARE_EXPIRES_AT,
        self::VERSION,
        self::IS_ENCRYPTED,
        self::ENCRYPTION_KEY,
        self::METADATA,
        self::OCR_TEXT,
        self::OCR_STATUS,
        self::OCR_COMPLETED_AT,
        self::THUMBNAIL_URL,
        self::PREVIEW_URL,
        self::DOWNLOAD_COUNT,
        self::LAST_DOWNLOADED_AT,
        self::LAST_DOWNLOADED_BY,
        self::STATUS,
        self::SYNC_STATUS,
        self::SYNC_MESSAGE,
        self::LAST_SYNC_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}