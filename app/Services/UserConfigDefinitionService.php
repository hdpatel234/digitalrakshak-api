<?php

namespace App\Services;

use App\Repositories\UserConfigDefinitionRepository;

/**
 * @property UserConfigDefinitionRepository $repository
 */
class UserConfigDefinitionService extends BaseService
{

    public function __construct(UserConfigDefinitionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function categoryId()
    {
        return $this->repository->categoryId();
    }

    public function configKey()
    {
        return $this->repository->configKey();
    }

    public function configName()
    {
        return $this->repository->configName();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function valueType()
    {
        return $this->repository->valueType();
    }

    public function defaultValue()
    {
        return $this->repository->defaultValue();
    }

    public function possibleValues()
    {
        return $this->repository->possibleValues();
    }

    public function validationRules()
    {
        return $this->repository->validationRules();
    }

    public function isRequired()
    {
        return $this->repository->isRequired();
    }

    public function isEditable()
    {
        return $this->repository->isEditable();
    }

    public function isPrivate()
    {
        return $this->repository->isPrivate();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function uiComponent()
    {
        return $this->repository->uiComponent();
    }

    public function uiProps()
    {
        return $this->repository->uiProps();
    }

    public function dependsOn()
    {
        return $this->repository->dependsOn();
    }

    // functions
}
