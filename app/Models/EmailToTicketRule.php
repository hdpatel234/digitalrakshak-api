<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailToTicketRule extends BaseModel
{
    use SoftDeletes;
    protected $table = "email_to_ticket_rules";
    const RULE_NAME = "rule_name";
    const RULE_PRIORITY = "rule_priority";
    const MATCH_TYPE = "match_type";
    const MATCH_VALUE = "match_value";
    const MATCH_PATTERN = "match_pattern";
    const TICKET_DEPARTMENT_ID = "ticket_department_id";
    const TICKET_PRIORITY_ID = "ticket_priority_id";
    const TICKET_CATEGORY = "ticket_category";
    const AUTO_ASSIGN_USER_ID = "auto_assign_user_id";
    const AUTO_RESPONSE_TEMPLATE_ID = "auto_response_template_id";
    const CREATE_TICKET = "create_ticket";
    const SEND_AUTO_RESPONSE = "send_auto_response";
    const TICKET_SUBJECT_PREFIX = "ticket_subject_prefix";
    const TICKET_SUBJECT_SUFFIX = "ticket_subject_suffix";
    const CUSTOMER_EMAIL_FIELD = "customer_email_field";
    const CUSTOMER_NAME_FIELD = "customer_name_field";
    const ESCALATE_AFTER_HOURS = "escalate_after_hours";
    const ESCALATE_USER_ID = "escalate_user_id";
    const ADDITIONAL_CONFIG = "additional_config";
    protected $fillable = [
        self::RULE_NAME,
        self::RULE_PRIORITY,
        self::MATCH_TYPE,
        self::MATCH_VALUE,
        self::MATCH_PATTERN,
        self::TICKET_DEPARTMENT_ID,
        self::TICKET_PRIORITY_ID,
        self::TICKET_CATEGORY,
        self::AUTO_ASSIGN_USER_ID,
        self::AUTO_RESPONSE_TEMPLATE_ID,
        self::CREATE_TICKET,
        self::SEND_AUTO_RESPONSE,
        self::TICKET_SUBJECT_PREFIX,
        self::TICKET_SUBJECT_SUFFIX,
        self::CUSTOMER_EMAIL_FIELD,
        self::CUSTOMER_NAME_FIELD,
        self::ESCALATE_AFTER_HOURS,
        self::ESCALATE_USER_ID,
        self::ADDITIONAL_CONFIG,
        self::STATUS
    ];
}
