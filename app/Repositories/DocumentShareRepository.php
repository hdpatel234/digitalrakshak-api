<?php

namespace App\Repositories;

use App\Models\DocumentShare;

class DocumentShareRepository extends BaseRepository
{
    public function __construct(DocumentShare $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function documentId()
    {
        return DocumentShare::DOCUMENT_ID;
    }

    public function shareToken()
    {
        return DocumentShare::SHARE_TOKEN;
    }

    public function shareType()
    {
        return DocumentShare::SHARE_TYPE;
    }

    public function password()
    {
        return DocumentShare::PASSWORD;
    }

    public function expiresAt()
    {
        return DocumentShare::EXPIRES_AT;
    }

    public function maxDownloads()
    {
        return DocumentShare::MAX_DOWNLOADS;
    }

    public function downloadCount()
    {
        return DocumentShare::DOWNLOAD_COUNT;
    }

    public function sharedWithEmail()
    {
        return DocumentShare::SHARED_WITH_EMAIL;
    }

    public function sharedWithName()
    {
        return DocumentShare::SHARED_WITH_NAME;
    }

    public function accessPermission()
    {
        return DocumentShare::ACCESS_PERMISSION;
    }

    public function createdBy()
    {
        return DocumentShare::CREATED_BY;
    }

    public function lastAccessedAt()
    {
        return DocumentShare::LAST_ACCESSED_AT;
    }
    // functions
}