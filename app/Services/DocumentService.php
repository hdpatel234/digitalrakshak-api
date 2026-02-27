<?php

namespace App\Services;

use App\Repositories\DocumentRepository;

class DocumentService extends BaseService
{
    protected $repository;
    
    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function documentConfigId()
    {
        return $this->repository->documentConfigId();
    }

    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function orderItemId()
    {
        return $this->repository->orderItemId();
    }

    public function invitationId()
    {
        return $this->repository->invitationId();
    }

    public function ticketId()
    {
        return $this->repository->ticketId();
    }

    public function documentType()
    {
        return $this->repository->documentType();
    }

    public function documentCategory()
    {
        return $this->repository->documentCategory();
    }

    public function originalFilename()
    {
        return $this->repository->originalFilename();
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

    public function mimeType()
    {
        return $this->repository->mimeType();
    }

    public function extension()
    {
        return $this->repository->extension();
    }

    public function externalFileId()
    {
        return $this->repository->externalFileId();
    }

    public function externalShareLink()
    {
        return $this->repository->externalShareLink();
    }

    public function externalShareId()
    {
        return $this->repository->externalShareId();
    }

    public function sharePassword()
    {
        return $this->repository->sharePassword();
    }

    public function shareExpiresAt()
    {
        return $this->repository->shareExpiresAt();
    }

    public function version()
    {
        return $this->repository->version();
    }

    public function isEncrypted()
    {
        return $this->repository->isEncrypted();
    }

    public function encryptionKey()
    {
        return $this->repository->encryptionKey();
    }

    public function metadata()
    {
        return $this->repository->metadata();
    }

    public function ocrText()
    {
        return $this->repository->ocrText();
    }

    public function ocrStatus()
    {
        return $this->repository->ocrStatus();
    }

    public function ocrCompletedAt()
    {
        return $this->repository->ocrCompletedAt();
    }

    public function thumbnailUrl()
    {
        return $this->repository->thumbnailUrl();
    }

    public function previewUrl()
    {
        return $this->repository->previewUrl();
    }

    public function downloadCount()
    {
        return $this->repository->downloadCount();
    }

    public function lastDownloadedAt()
    {
        return $this->repository->lastDownloadedAt();
    }

    public function lastDownloadedBy()
    {
        return $this->repository->lastDownloadedBy();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function syncStatus()
    {
        return $this->repository->syncStatus();
    }

    public function syncMessage()
    {
        return $this->repository->syncMessage();
    }

    public function lastSyncAt()
    {
        return $this->repository->lastSyncAt();
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