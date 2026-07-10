<?php

namespace App\Services;

use App\Repositories\CandidateImportHistoryRepository;

/**
 * @property CandidateImportHistoryRepository $repository
 */
class CandidateImportHistoryService extends BaseService
{
    
    public function __construct(CandidateImportHistoryRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function filename()
    {
        return $this->repository->filename();
    }

    public function totalRecords()
    {
        return $this->repository->totalRecords();
    }

    public function successfulImports()
    {
        return $this->repository->successfulImports();
    }

    public function failedImports()
    {
        return $this->repository->failedImports();
    }

    public function importedBy()
    {
        return $this->repository->importedBy();
    }

    public function errorLog()
    {
        return $this->repository->errorLog();
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