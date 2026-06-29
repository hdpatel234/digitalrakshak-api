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
        return DocumentTemplate::TEMPLATE_NAME;
    }

    public function templateCode()
    {
        return DocumentTemplate::TEMPLATE_CODE;
    }

    public function documentType()
    {
        return DocumentTemplate::DOCUMENT_TYPE;
    }

    public function templateFile()
    {
        return DocumentTemplate::TEMPLATE_FILE;
    }

    public function templateData()
    {
        return DocumentTemplate::TEMPLATE_DATA;
    }

    public function outputFormat()
    {
        return DocumentTemplate::OUTPUT_FORMAT;
    }

    public function isActive()
    {
        return DocumentTemplate::IS_ACTIVE;
    }

    public function createdBy()
    {
        return DocumentTemplate::CREATED_BY;
    }

    public function updatedBy()
    {
        return DocumentTemplate::UPDATED_BY;
    }
    // functions
}