<?php

namespace App\Repositories;

use App\Models\EmailServerConfigurationField;

class EmailServerConfigurationFieldRepository extends BaseRepository
{
    public function __construct(EmailServerConfigurationField $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serverTypeId()
    {
        return EmailServerConfigurationField::SERVER_TYPE_ID;
    }

    public function fieldName()
    {
        return EmailServerConfigurationField::FIELD_NAME;
    }

    public function fieldLabel()
    {
        return EmailServerConfigurationField::FIELD_LABEL;
    }

    public function fieldType()
    {
        return EmailServerConfigurationField::FIELD_TYPE;
    }

    public function isRequired()
    {
        return EmailServerConfigurationField::IS_REQUIRED;
    }

    public function defaultValue()
    {
        return EmailServerConfigurationField::DEFAULT_VALUE;
    }

    public function options()
    {
        return EmailServerConfigurationField::OPTIONS;
    }

    public function sortOrder()
    {
        return EmailServerConfigurationField::SORT_ORDER;
    }

    public function helpText()
    {
        return EmailServerConfigurationField::HELP_TEXT;
    }

    public function placeholder()
    {
        return EmailServerConfigurationField::PLACEHOLDER;
    }

    public function validationRules()
    {
        return EmailServerConfigurationField::VALIDATION_RULES;
    }

    public function isEncrypted()
    {
        return EmailServerConfigurationField::IS_ENCRYPTED;
    }
    // functions
}
