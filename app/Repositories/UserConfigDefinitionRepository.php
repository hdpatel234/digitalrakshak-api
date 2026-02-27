<?php

namespace App\Repositories;

use App\Models\UserConfigDefinition;

class UserConfigDefinitionRepository extends BaseRepository
{
    public function __construct(UserConfigDefinition $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function categoryId()
    {
        return $this->model::CATEGORY_ID;
    }

    public function configKey()
    {
        return $this->model::CONFIG_KEY;
    }

    public function configName()
    {
        return $this->model::CONFIG_NAME;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function valueType()
    {
        return $this->model::VALUE_TYPE;
    }

    public function defaultValue()
    {
        return $this->model::DEFAULT_VALUE;
    }

    public function possibleValues()
    {
        return $this->model::POSSIBLE_VALUES;
    }

    public function validationRules()
    {
        return $this->model::VALIDATION_RULES;
    }

    public function isRequired()
    {
        return $this->model::IS_REQUIRED;
    }

    public function isEditable()
    {
        return $this->model::IS_EDITABLE;
    }

    public function isPrivate()
    {
        return $this->model::IS_PRIVATE;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function uiComponent()
    {
        return $this->model::UI_COMPONENT;
    }

    public function uiProps()
    {
        return $this->model::UI_PROPS;
    }

    public function dependsOn()
    {
        return $this->model::DEPENDS_ON;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }
    // functions
}