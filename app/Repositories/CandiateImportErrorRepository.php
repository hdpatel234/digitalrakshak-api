<?php

namespace App\Repositories;

use App\Models\CandiateImportError;

class CandiateImportErrorRepository extends BaseRepository
{
    public function __construct(CandiateImportError $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function importId()
    {
        return CandiateImportError::IMPORT_ID;
    }

    public function rowNumber()
    {
        return CandiateImportError::ROW_NUMBER;
    }

    public function errorMessage()
    {
        return CandiateImportError::ERROR_MESSAGE;
    }

    public function rawData()
    {
        return CandiateImportError::RAW_DATA;
    }

    public function status()
    {
        return CandiateImportError::STATUS;
    }

    public function createdBy()
    {
        return CandiateImportError::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandiateImportError::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandiateImportError::DELETED_BY;
    }
    // functions
}