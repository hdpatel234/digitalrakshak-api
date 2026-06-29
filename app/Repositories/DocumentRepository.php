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
        return Document::CLIENT_ID;
    }

    public function documentConfigId()
    {
        return Document::DOCUMENT_CONFIG_ID;
    }

    public function candidateId()
    {
        return Document::CANDIDATE_ID;
    }

    public function orderId()
    {
        return Document::ORDER_ID;
    }

    public function orderItemId()
    {
        return Document::ORDER_ITEM_ID;
    }

    public function invitationId()
    {
        return Document::INVITATION_ID;
    }

    public function ticketId()
    {
        return Document::TICKET_ID;
    }

    public function documentType()
    {
        return Document::DOCUMENT_TYPE;
    }

    public function documentCategory()
    {
        return Document::DOCUMENT_CATEGORY;
    }

    public function originalFilename()
    {
        return Document::ORIGINAL_FILENAME;
    }

    public function storedFilename()
    {
        return Document::STORED_FILENAME;
    }

    public function filePath()
    {
        return Document::FILE_PATH;
    }

    public function fileSize()
    {
        return Document::FILE_SIZE;
    }

    public function fileHash()
    {
        return Document::FILE_HASH;
    }

    public function mimeType()
    {
        return Document::MIME_TYPE;
    }

    public function extension()
    {
        return Document::EXTENSION;
    }

    public function externalFileId()
    {
        return Document::EXTERNAL_FILE_ID;
    }

    public function externalShareLink()
    {
        return Document::EXTERNAL_SHARE_LINK;
    }

    public function externalShareId()
    {
        return Document::EXTERNAL_SHARE_ID;
    }

    public function sharePassword()
    {
        return Document::SHARE_PASSWORD;
    }

    public function shareExpiresAt()
    {
        return Document::SHARE_EXPIRES_AT;
    }

    public function version()
    {
        return Document::VERSION;
    }

    public function isEncrypted()
    {
        return Document::IS_ENCRYPTED;
    }

    public function encryptionKey()
    {
        return Document::ENCRYPTION_KEY;
    }

    public function metadata()
    {
        return Document::METADATA;
    }

    public function ocrText()
    {
        return Document::OCR_TEXT;
    }

    public function ocrStatus()
    {
        return Document::OCR_STATUS;
    }

    public function ocrCompletedAt()
    {
        return Document::OCR_COMPLETED_AT;
    }

    public function thumbnailUrl()
    {
        return Document::THUMBNAIL_URL;
    }

    public function previewUrl()
    {
        return Document::PREVIEW_URL;
    }

    public function downloadCount()
    {
        return Document::DOWNLOAD_COUNT;
    }

    public function lastDownloadedAt()
    {
        return Document::LAST_DOWNLOADED_AT;
    }

    public function lastDownloadedBy()
    {
        return Document::LAST_DOWNLOADED_BY;
    }

    public function status()
    {
        return Document::STATUS;
    }

    public function syncStatus()
    {
        return Document::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return Document::SYNC_MESSAGE;
    }

    public function lastSyncAt()
    {
        return Document::LAST_SYNC_AT;
    }

    public function createdBy()
    {
        return Document::CREATED_BY;
    }

    public function updatedBy()
    {
        return Document::UPDATED_BY;
    }
    // functions
}