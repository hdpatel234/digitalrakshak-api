<?php

namespace App\Repositories;

use App\Models\FieldVerificationRule;

class FieldVerificationRuleRepository extends BaseRepository
{
    public function __construct(FieldVerificationRule $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function fieldId()
    {
        return FieldVerificationRule::FIELD_ID;
    }

    public function ruleName()
    {
        return FieldVerificationRule::RULE_NAME;
    }

    public function ruleType()
    {
        return FieldVerificationRule::RULE_TYPE;
    }

    public function ruleConfig()
    {
        return FieldVerificationRule::RULE_CONFIG;
    }

    public function priority()
    {
        return FieldVerificationRule::PRIORITY;
    }

    public function failureAction()
    {
        return FieldVerificationRule::FAILURE_ACTION;
    }

    // functions
}
