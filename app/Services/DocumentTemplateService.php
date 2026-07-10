<?php

namespace App\Services;

use App\Repositories\DocumentTemplateRepository;

/**
 * @property DocumentTemplateRepository $repository
 */
class DocumentTemplateService extends BaseService
{
    
    public function __construct(DocumentTemplateRepository $repository)
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

    public function documentType()
    {
        return $this->repository->documentType();
    }

    public function templateFile()
    {
        return $this->repository->templateFile();
    }

    public function templateData()
    {
        return $this->repository->templateData();
    }

    public function outputFormat()
    {
        return $this->repository->outputFormat();
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