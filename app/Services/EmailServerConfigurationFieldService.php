<?php

namespace App\Services;

use App\Repositories\EmailServerConfigurationFieldRepository;

/**
 * @property EmailServerConfigurationFieldRepository $repository
 */
class EmailServerConfigurationFieldService extends BaseService
{
    public function __construct(EmailServerConfigurationFieldRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serverTypeId()
    {
        return $this->repository->serverTypeId();
    }

    public function fieldName()
    {
        return $this->repository->fieldName();
    }

    public function fieldLabel()
    {
        return $this->repository->fieldLabel();
    }

    public function fieldType()
    {
        return $this->repository->fieldType();
    }

    public function isRequired()
    {
        return $this->repository->isRequired();
    }

    public function defaultValue()
    {
        return $this->repository->defaultValue();
    }

    public function options()
    {
        return $this->repository->options();
    }

    public function sortOrder()
    {
        return $this->repository->sortOrder();
    }

    public function helpText()
    {
        return $this->repository->helpText();
    }

    public function placeholder()
    {
        return $this->repository->placeholder();
    }

    public function validationRules()
    {
        return $this->repository->validationRules();
    }

    public function isEncrypted()
    {
        return $this->repository->isEncrypted();
    }
    // functions
}
