<?php

namespace App\Repositories;

use App\Models\EmailTemplate;

class EmailTemplateRepository extends BaseRepository
{
    public function __construct(EmailTemplate $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serverId()
    {
        return $this->model::SERVER_ID;
    }
    
    public function templateName()
    {
        return $this->model::TEMPLATE_NAME;
    }

    public function templateCode()
    {
        return $this->model::TEMPLATE_CODE;
    }

    public function emailType()
    {
        return $this->model::EMAIL_TYPE;
    }

    public function subject()
    {
        return $this->model::SUBJECT;
    }

    public function bodyHtml()
    {
        return $this->model::BODY_HTML;
    }

    public function bodyText()
    {
        return $this->model::BODY_TEXT;
    }

    public function variables()
    {
        return $this->model::VARIABLES;
    }

    public function defaultPriority()
    {
        return $this->model::DEFAULT_PRIORITY;
    }

    public function allowedAttachments()
    {
        return $this->model::ALLOWED_ATTACHMENTS;
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