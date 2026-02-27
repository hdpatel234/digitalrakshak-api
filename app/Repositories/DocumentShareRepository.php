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
        return $this->model::DOCUMENT_ID;
    }

    public function shareToken()
    {
        return $this->model::SHARE_TOKEN;
    }

    public function shareType()
    {
        return $this->model::SHARE_TYPE;
    }

    public function password()
    {
        return $this->model::PASSWORD;
    }

    public function expiresAt()
    {
        return $this->model::EXPIRES_AT;
    }

    public function maxDownloads()
    {
        return $this->model::MAX_DOWNLOADS;
    }

    public function downloadCount()
    {
        return $this->model::DOWNLOAD_COUNT;
    }

    public function sharedWithEmail()
    {
        return $this->model::SHARED_WITH_EMAIL;
    }

    public function sharedWithName()
    {
        return $this->model::SHARED_WITH_NAME;
    }

    public function accessPermission()
    {
        return $this->model::ACCESS_PERMISSION;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function lastAccessedAt()
    {
        return $this->model::LAST_ACCESSED_AT;
    }
    // functions
}