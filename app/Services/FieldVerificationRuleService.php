<?php

namespace App\Services;

use App\Repositories\FieldVerificationRuleRepository;

/**
 * @property FieldVerificationRuleRepository $repository
 */
class FieldVerificationRuleService extends BaseService
{
    public function __construct(protected FieldVerificationRuleRepository $repository) {}

    // column constants
    public function fieldId()
    {
        return $this->repository->fieldId();
    }

    public function ruleName()
    {
        return $this->repository->ruleName();
    }

    public function ruleType()
    {
        return $this->repository->ruleType();
    }

    public function ruleConfig()
    {
        return $this->repository->ruleConfig();
    }

    public function priority()
    {
        return $this->repository->priority();
    }

    public function failureAction()
    {
        return $this->repository->failureAction();
    }

    // functions
}
