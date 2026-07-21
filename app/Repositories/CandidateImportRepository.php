<?php

namespace App\Repositories;

use App\Models\CandidateImport;

class CandidateImportRepository extends BaseRepository
{
    public function __construct(CandidateImport $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return CandidateImport::CLIENT_ID;
    }

    public function filename()
    {
        return CandidateImport::FILENAME;
    }

    public function totalRecords()
    {
        return CandidateImport::TOTAL_RECORDS;
    }

    public function successfulImports()
    {
        return CandidateImport::SUCCESSFUL_IMPORTS;
    }

    public function failedImports()
    {
        return CandidateImport::FAILED_IMPORTS;
    }

    public function importedBy()
    {
        return CandidateImport::IMPORTED_BY;
    }

    public function jsonData()
    {
        return CandidateImport::JSON_DATA;
    }

    public function errorLog()
    {
        return CandidateImport::ERROR_LOG;
    }

    public function status()
    {
        return CandidateImport::STATUS;
    }

    public function reason()
    {
        return CandidateImport::REASON;
    }

    // functions
}
