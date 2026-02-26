<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends BaseModel
{
    
    protected $table = "email_templates";
    
    const TEMPLATE_NAME = "template_name";
    const TEMPLATE_CODE = "template_code";
    const SUBJECT = "subject";
    const BODY = "body";
    const VARIABLES = "variables";
    const TYPE = "type";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::TEMPLATE_NAME,
        self::TEMPLATE_CODE,
        self::SUBJECT,
        self::BODY,
        self::VARIABLES,
        self::TYPE,
        self::IS_ACTIVE,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}