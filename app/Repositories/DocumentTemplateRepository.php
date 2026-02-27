<?php

namespace App\Repositories;

use App\Models\DocumentTemplate;

class DocumentTemplateRepository extends BaseRepository
{
    public function __construct(DocumentTemplate $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function templateName()
    {
        return $this->model::TEMPLATE_NAME;
    }

    public function templateCode()
    {
        return $this->model::TEMPLATE_CODE;
    }

    public function documentType()
    {
        return $this->model::DOCUMENT_TYPE;
    }

    public function templateFile()
    {
        return $this->model::TEMPLATE_FILE;
    }

    public function templateData()
    {
        return $this->model::TEMPLATE_DATA;
    }

    public function outputFormat()
    {
        return $this->model::OUTPUT_FORMAT;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
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