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
        return $this->model::TEMPLATE_ID;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function documentConfigId()
    {
        return $this->model::DOCUMENT_CONFIG_ID;
    }

    public function documentId()
    {
        return $this->model::DOCUMENT_ID;
    }

    public function referenceType()
    {
        return $this->model::REFERENCE_TYPE;
    }

    public function referenceId()
    {
        return $this->model::REFERENCE_ID;
    }

    public function documentNumber()
    {
        return $this->model::DOCUMENT_NUMBER;
    }

    public function title()
    {
        return $this->model::TITLE;
    }

    public function generatedData()
    {
        return $this->model::GENERATED_DATA;
    }

    public function filePath()
    {
        return $this->model::FILE_PATH;
    }

    public function fileSize()
    {
        return $this->model::FILE_SIZE;
    }

    public function generatedAt()
    {
        return $this->model::GENERATED_AT;
    }

    public function generatedBy()
    {
        return $this->model::GENERATED_BY;
    }

    public function downloadCount()
    {
        return $this->model::DOWNLOAD_COUNT;
    }
    // functions
}