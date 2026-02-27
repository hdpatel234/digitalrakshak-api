<?php

namespace App\Services;

use App\Repositories\DocumentOcrQueueRepository;

class DocumentOcrQueueService extends BaseService
{
    protected $repository;
    
    public function __construct(DocumentOcrQueueRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function documentId()
    {
        return $this->repository->documentId();
    }

    public function priority()
    {
        return $this->repository->priority();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function attempts()
    {
        return $this->repository->attempts();
    }

    public function maxAttempts()
    {
        return $this->repository->maxAttempts();
    }

    public function ocrText()
    {
        return $this->repository->ocrText();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function processedAt()
    {
        return $this->repository->processedAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
    }
    // functions
}