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
        return EmailTemplate::SERVER_ID;
    }
    
    public function templateName()
    {
        return EmailTemplate::TEMPLATE_NAME;
    }

    public function templateCode()
    {
        return EmailTemplate::TEMPLATE_CODE;
    }

    public function emailType()
    {
        return EmailTemplate::EMAIL_TYPE;
    }

    public function subject()
    {
        return EmailTemplate::SUBJECT;
    }

    public function bodyHtml()
    {
        return EmailTemplate::BODY_HTML;
    }

    public function bodyText()
    {
        return EmailTemplate::BODY_TEXT;
    }

    public function variables()
    {
        return EmailTemplate::VARIABLES;
    }

    public function defaultPriority()
    {
        return EmailTemplate::DEFAULT_PRIORITY;
    }

    public function allowedAttachments()
    {
        return EmailTemplate::ALLOWED_ATTACHMENTS;
    }

    public function isActive()
    {
        return EmailTemplate::IS_ACTIVE;
    }

    public function createdBy()
    {
        return EmailTemplate::CREATED_BY;
    }

    public function updatedBy()
    {
        return EmailTemplate::UPDATED_BY;
    }
    // functions
    public function findActiveByCode(string $code)
    {
        return $this->query()
            ->where($this->templateCode(), $code)
            ->where($this->isActive(), true)
            ->first();
    }
}
