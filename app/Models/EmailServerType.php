<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailServerType extends BaseModel
{
    
    protected $table = "email_server_types";
    
    const TYPE_NAME = "type_name";
    const TYPE_CODE = "type_code";
    const DESCRIPTION = "description";
    const IS_OUTGOING = "is_outgoing";
    const IS_INCOMING = "is_incoming";
    const CONFIGURATION_SCHEMA = "configuration_schema";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::TYPE_NAME,
        self::TYPE_CODE,
        self::DESCRIPTION,
        self::IS_OUTGOING,
        self::IS_INCOMING,
        self::CONFIGURATION_SCHEMA,
        self::IS_ACTIVE,
    ];
}