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
        return $this->model::IMPORT_ID;
    }

    public function rowNumber()
    {
        return $this->model::ROW_NUMBER;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function rawData()
    {
        return $this->model::RAW_DATA;
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