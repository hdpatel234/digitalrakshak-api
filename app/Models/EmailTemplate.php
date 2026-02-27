<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends BaseModel
{
    
    protected $table = "email_templates";
    
    const TEMPLATE_NAME = "template_name";
    const TEMPLATE_CODE = "template_code";
    const EMAIL_TYPE = "email_type";
    const SUBJECT = "subject";
    const BODY_HTML = "body_html";
    const BODY_TEXT = "body_text";
    const VARIABLES = "variables";
    const DEFAULT_PRIORITY = "default_priority";
    const ALLOWED_ATTACHMENTS = "allowed_attachments";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::TEMPLATE_NAME,
        self::TEMPLATE_CODE,
        self::EMAIL_TYPE,
        self::SUBJECT,
        self::BODY_HTML,
        self::BODY_TEXT,
        self::VARIABLES,
        self::DEFAULT_PRIORITY,
        self::ALLOWED_ATTACHMENTS,
        self::IS_ACTIVE,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}