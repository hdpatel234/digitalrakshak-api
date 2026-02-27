<?php

namespace App\Services;

use App\Repositories\DocumentVersionRepository;

class DocumentVersionService extends BaseService
{
    protected $repository;
    
    public function __construct(DocumentVersionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function documentId()
    {
        return $this->repository->documentId();
    }

    public function versionNumber()
    {
        return $this->repository->versionNumber();
    }

    public function storedFilename()
    {
        return $this->repository->storedFilename();
    }

    public function filePath()
    {
        return $this->repository->filePath();
    }

    public function fileSize()
    {
        return $this->repository->fileSize();
    }

    public function fileHash()
    {
        return $this->repository->fileHash();
    }

    public function externalFileId()
    {
        return $this->repository->externalFileId();
    }

    public function changeReason()
    {
        return $this->repository->changeReason();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }
    // functions
}