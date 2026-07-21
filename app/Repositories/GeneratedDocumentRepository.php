<?php

namespace App\Repositories;

use App\Models\GeneratedDocument;

class GeneratedDocumentRepository extends BaseRepository
{
    public function __construct(GeneratedDocument $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function templateId()
    {
        return GeneratedDocument::TEMPLATE_ID;
    }

    public function clientId()
    {
        return GeneratedDocument::CLIENT_ID;
    }

    public function documentConfigId()
    {
        return GeneratedDocument::DOCUMENT_CONFIG_ID;
    }

    public function documentId()
    {
        return GeneratedDocument::DOCUMENT_ID;
    }

    public function referenceType()
    {
        return GeneratedDocument::REFERENCE_TYPE;
    }

    public function referenceId()
    {
        return GeneratedDocument::REFERENCE_ID;
    }

    public function documentNumber()
    {
        return GeneratedDocument::DOCUMENT_NUMBER;
    }

    public function title()
    {
        return GeneratedDocument::TITLE;
    }

    public function generatedData()
    {
        return GeneratedDocument::GENERATED_DATA;
    }

    public function filePath()
    {
        return GeneratedDocument::FILE_PATH;
    }

    public function fileSize()
    {
        return GeneratedDocument::FILE_SIZE;
    }

    public function generatedAt()
    {
        return GeneratedDocument::GENERATED_AT;
    }

    public function generatedBy()
    {
        return GeneratedDocument::GENERATED_BY;
    }

    public function downloadCount()
    {
        return GeneratedDocument::DOWNLOAD_COUNT;
    }
    // functions
}
