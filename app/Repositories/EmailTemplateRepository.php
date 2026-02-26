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
    public function templateName()
    {
        return $this->model::TEMPLATE_NAME;
    }

    public function templateCode()
    {
        return $this->model::TEMPLATE_CODE;
    }

    public function subject()
    {
        return $this->model::SUBJECT;
    }

    public function body()
    {
        return $this->model::BODY;
    }

    public function variables()
    {
        return $this->model::VARIABLES;
    }

    public function type()
    {
        return $this->model::TYPE;
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