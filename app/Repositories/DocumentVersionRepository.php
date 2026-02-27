<?php

namespace App\Repositories;

use App\Models\DocumentVersion;

class DocumentVersionRepository extends BaseRepository
{
    public function __construct(DocumentVersion $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function documentId()
    {
        return $this->model::DOCUMENT_ID;
    }

    public function versionNumber()
    {
        return $this->model::VERSION_NUMBER;
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

    public function externalFileId()
    {
        return $this->model::EXTERNAL_FILE_ID;
    }

    public function changeReason()
    {
        return $this->model::CHANGE_REASON;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }
    // functions
}