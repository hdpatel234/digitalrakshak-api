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
        return $this->model::CLIENT_ID;
    }

    public function filename()
    {
        return $this->model::FILENAME;
    }

    public function totalRecords()
    {
        return $this->model::TOTAL_RECORDS;
    }

    public function successfulImports()
    {
        return $this->model::SUCCESSFUL_IMPORTS;
    }

    public function failedImports()
    {
        return $this->model::FAILED_IMPORTS;
    }

    public function importedBy()
    {
        return $this->model::IMPORTED_BY;
    }

    public function errorLog()
    {
        return $this->model::ERROR_LOG;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}