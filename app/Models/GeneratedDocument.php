<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class GeneratedDocument extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "generated_documents";
    
    const TEMPLATE_ID = "template_id";
    const CLIENT_ID = "client_id";
    const DOCUMENT_CONFIG_ID = "document_config_id";
    const DOCUMENT_ID = "document_id";
    const REFERENCE_TYPE = "reference_type";
    const REFERENCE_ID = "reference_id";
    const DOCUMENT_NUMBER = "document_number";
    const TITLE = "title";
    const GENERATED_DATA = "generated_data";
    const FILE_PATH = "file_path";
    const FILE_SIZE = "file_size";
    const GENERATED_AT = "generated_at";
    const GENERATED_BY = "generated_by";
    const DOWNLOAD_COUNT = "download_count";
    protected $fillable = [
        self::TEMPLATE_ID,
        self::CLIENT_ID,
        self::DOCUMENT_CONFIG_ID,
        self::DOCUMENT_ID,
        self::REFERENCE_TYPE,
        self::REFERENCE_ID,
        self::DOCUMENT_NUMBER,
        self::TITLE,
        self::GENERATED_DATA,
        self::FILE_PATH,
        self::FILE_SIZE,
        self::GENERATED_AT,
        self::GENERATED_BY,
        self::DOWNLOAD_COUNT,
    ];
}