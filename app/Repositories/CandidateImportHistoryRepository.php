<?php

namespace App\Repositories;

use App\Models\CandidateImportHistory;

class CandidateImportHistoryRepository extends BaseRepository
{
    public function __construct(CandidateImportHistory $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return CandidateImportHistory::CLIENT_ID;
    }

    public function filename()
    {
        return CandidateImportHistory::FILENAME;
    }

    public function totalRecords()
    {
        return CandidateImportHistory::TOTAL_RECORDS;
    }

    public function successfulImports()
    {
        return CandidateImportHistory::SUCCESSFUL_IMPORTS;
    }

    public function failedImports()
    {
        return CandidateImportHistory::FAILED_IMPORTS;
    }

    public function importedBy()
    {
        return CandidateImportHistory::IMPORTED_BY;
    }

    public function errorLog()
    {
        return CandidateImportHistory::ERROR_LOG;
    }

    public function status()
    {
        return CandidateImportHistory::STATUS;
    }

    public function createdBy()
    {
        return CandidateImportHistory::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateImportHistory::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateImportHistory::DELETED_BY;
    }
    // functions
}