<?php

namespace App\Services;

use App\Repositories\DocumentShareRepository;

/**
 * @property DocumentShareRepository $repository
 */
class DocumentShareService extends BaseService
{
    
    public function __construct(DocumentShareRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function documentId()
    {
        return $this->repository->documentId();
    }

    public function shareToken()
    {
        return $this->repository->shareToken();
    }

    public function shareType()
    {
        return $this->repository->shareType();
    }

    public function password()
    {
        return $this->repository->password();
    }

    public function expiresAt()
    {
        return $this->repository->expiresAt();
    }

    public function maxDownloads()
    {
        return $this->repository->maxDownloads();
    }

    public function downloadCount()
    {
        return $this->repository->downloadCount();
    }

    public function sharedWithEmail()
    {
        return $this->repository->sharedWithEmail();
    }

    public function sharedWithName()
    {
        return $this->repository->sharedWithName();
    }

    public function accessPermission()
    {
        return $this->repository->accessPermission();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function lastAccessedAt()
    {
        return $this->repository->lastAccessedAt();
    }
    // functions
}