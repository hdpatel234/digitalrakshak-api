<?php

namespace App\Services;

use App\Repositories\GeneratedDocumentRepository;

/**
 * @property GeneratedDocumentRepository $repository
 */
class GeneratedDocumentService extends BaseService
{
    
    public function __construct(GeneratedDocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function templateId()
    {
        return $this->repository->templateId();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function documentConfigId()
    {
        return $this->repository->documentConfigId();
    }

    public function documentId()
    {
        return $this->repository->documentId();
    }

    public function referenceType()
    {
        return $this->repository->referenceType();
    }

    public function referenceId()
    {
        return $this->repository->referenceId();
    }

    public function documentNumber()
    {
        return $this->repository->documentNumber();
    }

    public function title()
    {
        return $this->repository->title();
    }

    public function generatedData()
    {
        return $this->repository->generatedData();
    }

    public function filePath()
    {
        return $this->repository->filePath();
    }

    public function fileSize()
    {
        return $this->repository->fileSize();
    }

    public function generatedAt()
    {
        return $this->repository->generatedAt();
    }

    public function generatedBy()
    {
        return $this->repository->generatedBy();
    }

    public function downloadCount()
    {
        return $this->repository->downloadCount();
    }
    // functions
}