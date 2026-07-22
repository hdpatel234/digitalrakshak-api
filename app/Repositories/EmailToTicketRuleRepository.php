<?php

namespace App\Repositories;

use App\Models\EmailToTicketRule;

class EmailToTicketRuleRepository extends BaseRepository
{
    public function __construct(EmailToTicketRule $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function ruleName()
    {
        return EmailToTicketRule::RULE_NAME;
    }

    public function rulePriority()
    {
        return EmailToTicketRule::RULE_PRIORITY;
    }

    public function matchType()
    {
        return EmailToTicketRule::MATCH_TYPE;
    }

    public function matchValue()
    {
        return EmailToTicketRule::MATCH_VALUE;
    }

    public function matchPattern()
    {
        return EmailToTicketRule::MATCH_PATTERN;
    }

    public function ticketDepartmentId()
    {
        return EmailToTicketRule::TICKET_DEPARTMENT_ID;
    }

    public function ticketPriorityId()
    {
        return EmailToTicketRule::TICKET_PRIORITY_ID;
    }

    public function ticketCategory()
    {
        return EmailToTicketRule::TICKET_CATEGORY;
    }

    public function autoAssignUserId()
    {
        return EmailToTicketRule::AUTO_ASSIGN_USER_ID;
    }

    public function autoResponseTemplateId()
    {
        return EmailToTicketRule::AUTO_RESPONSE_TEMPLATE_ID;
    }

    public function createTicket()
    {
        return EmailToTicketRule::CREATE_TICKET;
    }

    public function sendAutoResponse()
    {
        return EmailToTicketRule::SEND_AUTO_RESPONSE;
    }

    public function ticketSubjectPrefix()
    {
        return EmailToTicketRule::TICKET_SUBJECT_PREFIX;
    }

    public function ticketSubjectSuffix()
    {
        return EmailToTicketRule::TICKET_SUBJECT_SUFFIX;
    }

    public function customerEmailField()
    {
        return EmailToTicketRule::CUSTOMER_EMAIL_FIELD;
    }

    public function customerNameField()
    {
        return EmailToTicketRule::CUSTOMER_NAME_FIELD;
    }

    public function escalateAfterHours()
    {
        return EmailToTicketRule::ESCALATE_AFTER_HOURS;
    }

    public function escalateUserId()
    {
        return EmailToTicketRule::ESCALATE_USER_ID;
    }

    public function additionalConfig()
    {
        return EmailToTicketRule::ADDITIONAL_CONFIG;
    }

    // functions
}
