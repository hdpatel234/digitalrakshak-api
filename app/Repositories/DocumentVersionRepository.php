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
        return DocumentVersion::DOCUMENT_ID;
    }

    public function versionNumber()
    {
        return DocumentVersion::VERSION_NUMBER;
    }

    public function storedFilename()
    {
        return DocumentVersion::STORED_FILENAME;
    }

    public function filePath()
    {
        return DocumentVersion::FILE_PATH;
    }

    public function fileSize()
    {
        return DocumentVersion::FILE_SIZE;
    }

    public function fileHash()
    {
        return DocumentVersion::FILE_HASH;
    }

    public function externalFileId()
    {
        return DocumentVersion::EXTERNAL_FILE_ID;
    }

    public function changeReason()
    {
        return DocumentVersion::CHANGE_REASON;
    }

    public function createdBy()
    {
        return DocumentVersion::CREATED_BY;
    }
    // functions
}