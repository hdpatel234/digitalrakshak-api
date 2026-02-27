<?php

namespace App\Repositories;

use App\Models\EmailRoutingRule;

class EmailRoutingRuleRepository extends BaseRepository
{
    public function __construct(EmailRoutingRule $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function ruleName()
    {
        return $this->model::RULE_NAME;
    }

    public function rulePriority()
    {
        return $this->model::RULE_PRIORITY;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function matchType()
    {
        return $this->model::MATCH_TYPE;
    }

    public function matchValue()
    {
        return $this->model::MATCH_VALUE;
    }

    public function matchPattern()
    {
        return $this->model::MATCH_PATTERN;
    }

    public function emailType()
    {
        return $this->model::EMAIL_TYPE;
    }

    public function actionType()
    {
        return $this->model::ACTION_TYPE;
    }

    public function serverId()
    {
        return $this->model::SERVER_ID;
    }

    public function serverGroup()
    {
        return $this->model::SERVER_GROUP;
    }

    public function failoverServerId()
    {
        return $this->model::FAILOVER_SERVER_ID;
    }

    public function maxRetries()
    {
        return $this->model::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return $this->model::RETRY_DELAY_SECONDS;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function timeStart()
    {
        return $this->model::TIME_START;
    }

    public function timeEnd()
    {
        return $this->model::TIME_END;
    }

    public function daysOfWeek()
    {
        return $this->model::DAYS_OF_WEEK;
    }

    public function timesUsed()
    {
        return $this->model::TIMES_USED;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
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