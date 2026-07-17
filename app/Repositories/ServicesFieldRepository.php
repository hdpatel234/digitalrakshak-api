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
        return ServicesField::SERVICE_ID;
    }

    public function fieldName()
    {
        return ServicesField::FIELD_NAME;
    }

    public function fieldLabel()
    {
        return ServicesField::FIELD_LABEL;
    }

    public function fieldType()
    {
        return ServicesField::FIELD_TYPE;
    }

    public function isRequired()
    {
        return ServicesField::IS_REQUIRED;
    }

    public function validationRegex()
    {
        return ServicesField::VALIDATION_REGEX;
    }

    public function displayOrder()
    {
        return ServicesField::DISPLAY_ORDER;
    }

    public function status()
    {
        return ServicesField::STATUS;
    }

    public function createdBy()
    {
        return ServicesField::CREATED_BY;
    }

    public function updatedBy()
    {
        return ServicesField::UPDATED_BY;
    }

    public function deletedBy()
    {
        return ServicesField::DELETED_BY;
    }

    public function section()
    {
        return ServicesField::SECTION;
    }
    // functions
}