<?php

namespace App\Repositories;

use App\Models\ServicesField;

class ServicesFieldRepository extends BaseRepository
{
    public function __construct(ServicesField $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function fieldName()
    {
        return $this->model::FIELD_NAME;
    }

    public function fieldLabel()
    {
        return $this->model::FIELD_LABEL;
    }

    public function fieldType()
    {
        return $this->model::FIELD_TYPE;
    }

    public function isRequired()
    {
        return $this->model::IS_REQUIRED;
    }

    public function validationRegex()
    {
        return $this->model::VALIDATION_REGEX;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}