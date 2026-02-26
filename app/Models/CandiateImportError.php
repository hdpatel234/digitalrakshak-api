<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CandiateImportError extends BaseModel
{
    
    protected $table = "candiate_import_errors";
    
    const IMPORT_ID = "import_id";
    const ROW_NUMBER = "row_number";
    const ERROR_MESSAGE = "error_message";
    const RAW_DATA = "raw_data";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::IMPORT_ID,
        self::ROW_NUMBER,
        self::ERROR_MESSAGE,
        self::RAW_DATA,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}