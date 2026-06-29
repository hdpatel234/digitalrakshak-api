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
        return EmailRoutingRule::RULE_NAME;
    }

    public function rulePriority()
    {
        return EmailRoutingRule::RULE_PRIORITY;
    }

    public function isActive()
    {
        return EmailRoutingRule::IS_ACTIVE;
    }

    public function matchType()
    {
        return EmailRoutingRule::MATCH_TYPE;
    }

    public function matchValue()
    {
        return EmailRoutingRule::MATCH_VALUE;
    }

    public function matchPattern()
    {
        return EmailRoutingRule::MATCH_PATTERN;
    }

    public function emailType()
    {
        return EmailRoutingRule::EMAIL_TYPE;
    }

    public function actionType()
    {
        return EmailRoutingRule::ACTION_TYPE;
    }

    public function serverId()
    {
        return EmailRoutingRule::SERVER_ID;
    }

    public function serverGroup()
    {
        return EmailRoutingRule::SERVER_GROUP;
    }

    public function failoverServerId()
    {
        return EmailRoutingRule::FAILOVER_SERVER_ID;
    }

    public function maxRetries()
    {
        return EmailRoutingRule::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return EmailRoutingRule::RETRY_DELAY_SECONDS;
    }

    public function clientId()
    {
        return EmailRoutingRule::CLIENT_ID;
    }

    public function timeStart()
    {
        return EmailRoutingRule::TIME_START;
    }

    public function timeEnd()
    {
        return EmailRoutingRule::TIME_END;
    }

    public function daysOfWeek()
    {
        return EmailRoutingRule::DAYS_OF_WEEK;
    }

    public function timesUsed()
    {
        return EmailRoutingRule::TIMES_USED;
    }

    public function lastUsedAt()
    {
        return EmailRoutingRule::LAST_USED_AT;
    }

    public function createdBy()
    {
        return EmailRoutingRule::CREATED_BY;
    }

    public function updatedBy()
    {
        return EmailRoutingRule::UPDATED_BY;
    }
    // functions
}