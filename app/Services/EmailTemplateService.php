<?php

namespace App\Services;

use App\Repositories\EmailTemplateRepository;

class EmailTemplateService extends BaseService
{
    protected $repository;
    
    public function __construct(EmailTemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function templateName()
    {
        return $this->repository->templateName();
    }

    public function templateCode()
    {
        return $this->repository->templateCode();
    }

    public function emailType()
    {
        return $this->repository->emailType();
    }

    public function subject()
    {
        return $this->repository->subject();
    }

    public function bodyHtml()
    {
        return $this->repository->bodyHtml();
    }

    public function bodyText()
    {
        return $this->repository->bodyText();
    }

    public function variables()
    {
        return $this->repository->variables();
    }

    public function defaultPriority()
    {
        return $this->repository->defaultPriority();
    }

    public function allowedAttachments()
    {
        return $this->repository->allowedAttachments();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}