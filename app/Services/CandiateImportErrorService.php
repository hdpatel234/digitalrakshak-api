<?php

namespace App\Services;

use App\Repositories\CandiateImportErrorRepository;

/**
 * @property CandiateImportErrorRepository $repository
 */
class CandiateImportErrorService extends BaseService
{
    
    public function __construct(CandiateImportErrorRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function importId()
    {
        return $this->repository->importId();
    }

    public function rowNumber()
    {
        return $this->repository->rowNumber();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function rawData()
    {
        return $this->repository->rawData();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    // functions
}
