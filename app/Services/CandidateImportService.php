<?php

namespace App\Services;

use App\Repositories\CandidateImportRepository;

/**
 * @property CandidateImportRepository $repository
 */
class CandidateImportService extends BaseService
{

    public function __construct(CandidateImportRepository $repository)
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

    public function jsonData()
    {
        return $this->repository->jsonData();
    }

    public function errorLog()
    {
        return $this->repository->errorLog();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function reason()
    {
        return $this->repository->reason();
    }

    // functions
}
