<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateImport extends BaseModel
{
    use SoftDeletes;

    protected $table = "candidate_imports";

    const CLIENT_ID = "client_id";
    const FILENAME = "filename";
    const TOTAL_RECORDS = "total_records";
    const SUCCESSFUL_IMPORTS = "successful_imports";
    const FAILED_IMPORTS = "failed_imports";
    const IMPORTED_BY = "imported_by";
    const JSON_DATA = "json_data";
    const ERROR_LOG = "error_log";
    const STATUS = "status";
    const REASON = "reason";
    protected $fillable = [
        self::CLIENT_ID,
        self::FILENAME,
        self::TOTAL_RECORDS,
        self::SUCCESSFUL_IMPORTS,
        self::FAILED_IMPORTS,
        self::IMPORTED_BY,
        self::JSON_DATA,
        self::ERROR_LOG,
        self::STATUS,
        self::REASON,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function importErrors(): HasMany
    {
        return $this->hasMany(CandiateImportError::class, CandiateImportError::IMPORT_ID);
    }
}
