<?php

namespace App\Services;

use App\Repositories\DocumentConfigRepository;

class DocumentConfigService extends BaseService
{
    protected $repository;
    public function __construct(DocumentConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function documentPlatformId()
    {
        return $this->repository->documentPlatformId();
    }

    public function configName()
    {
        return $this->repository->configName();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function apiUrl()
    {
        return $this->repository->apiUrl();
    }

    public function username()
    {
        return $this->repository->username();
    }

    public function password()
    {
        return $this->repository->password();
    }

    public function apiKey()
    {
        return $this->repository->apiKey();
    }

    public function apiSecret()
    {
        return $this->repository->apiSecret();
    }

    public function accessToken()
    {
        return $this->repository->accessToken();
    }

    public function refreshToken()
    {
        return $this->repository->refreshToken();
    }

    public function tokenExpiresAt()
    {
        return $this->repository->tokenExpiresAt();
    }

    public function rootFolder()
    {
        return $this->repository->rootFolder();
    }

    public function clientFolder()
    {
        return $this->repository->clientFolder();
    }

    public function folderStructure()
    {
        return $this->repository->folderStructure();
    }

    public function fileNamingConvention()
    {
        return $this->repository->fileNamingConvention();
    }

    public function maxFileSize()
    {
        return $this->repository->maxFileSize();
    }

    public function allowedFileTypes()
    {
        return $this->repository->allowedFileTypes();
    }

    public function isPublicReadable()
    {
        return $this->repository->isPublicReadable();
    }

    public function shareExpiryDays()
    {
        return $this->repository->shareExpiryDays();
    }

    public function webhookSecret()
    {
        return $this->repository->webhookSecret();
    }

    public function additionalConfig()
    {
        return $this->repository->additionalConfig();
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
    // functions
}