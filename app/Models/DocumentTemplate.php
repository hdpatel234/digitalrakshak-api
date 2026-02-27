<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends BaseModel
{
    
    protected $table = "document_templates";
    
    const TEMPLATE_NAME = "template_name";
    const TEMPLATE_CODE = "template_code";
    const DOCUMENT_TYPE = "document_type";
    const TEMPLATE_FILE = "template_file";
    const TEMPLATE_DATA = "template_data";
    const OUTPUT_FORMAT = "output_format";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::TEMPLATE_NAME,
        self::TEMPLATE_CODE,
        self::DOCUMENT_TYPE,
        self::TEMPLATE_FILE,
        self::TEMPLATE_DATA,
        self::OUTPUT_FORMAT,
        self::IS_ACTIVE,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}