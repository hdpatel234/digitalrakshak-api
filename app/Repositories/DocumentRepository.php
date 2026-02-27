<?php

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository extends BaseRepository
{
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function documentConfigId()
    {
        return $this->model::DOCUMENT_CONFIG_ID;
    }

    public function candidateId()
    {
        return $this->model::CANDIDATE_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function orderItemId()
    {
        return $this->model::ORDER_ITEM_ID;
    }

    public function invitationId()
    {
        return $this->model::INVITATION_ID;
    }

    public function ticketId()
    {
        return $this->model::TICKET_ID;
    }

    public function documentType()
    {
        return $this->model::DOCUMENT_TYPE;
    }

    public function documentCategory()
    {
        return $this->model::DOCUMENT_CATEGORY;
    }

    public function originalFilename()
    {
        return $this->model::ORIGINAL_FILENAME;
    }

    public function storedFilename()
    {
        return $this->model::STORED_FILENAME;
    }

    public function filePath()
    {
        return $this->model::FILE_PATH;
    }

    public function fileSize()
    {
        return $this->model::FILE_SIZE;
    }

    public function fileHash()
    {
        return $this->model::FILE_HASH;
    }

    public function mimeType()
    {
        return $this->model::MIME_TYPE;
    }

    public function extension()
    {
        return $this->model::EXTENSION;
    }

    public function externalFileId()
    {
        return $this->model::EXTERNAL_FILE_ID;
    }

    public function externalShareLink()
    {
        return $this->model::EXTERNAL_SHARE_LINK;
    }

    public function externalShareId()
    {
        return $this->model::EXTERNAL_SHARE_ID;
    }

    public function sharePassword()
    {
        return $this->model::SHARE_PASSWORD;
    }

    public function shareExpiresAt()
    {
        return $this->model::SHARE_EXPIRES_AT;
    }

    public function version()
    {
        return $this->model::VERSION;
    }

    public function isEncrypted()
    {
        return $this->model::IS_ENCRYPTED;
    }

    public function encryptionKey()
    {
        return $this->model::ENCRYPTION_KEY;
    }

    public function metadata()
    {
        return $this->model::METADATA;
    }

    public function ocrText()
    {
        return $this->model::OCR_TEXT;
    }

    public function ocrStatus()
    {
        return $this->model::OCR_STATUS;
    }

    public function ocrCompletedAt()
    {
        return $this->model::OCR_COMPLETED_AT;
    }

    public function thumbnailUrl()
    {
        return $this->model::THUMBNAIL_URL;
    }

    public function previewUrl()
    {
        return $this->model::PREVIEW_URL;
    }

    public function downloadCount()
    {
        return $this->model::DOWNLOAD_COUNT;
    }

    public function lastDownloadedAt()
    {
        return $this->model::LAST_DOWNLOADED_AT;
    }

    public function lastDownloadedBy()
    {
        return $this->model::LAST_DOWNLOADED_BY;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function syncStatus()
    {
        return $this->model::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return $this->model::SYNC_MESSAGE;
    }

    public function lastSyncAt()
    {
        return $this->model::LAST_SYNC_AT;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}