<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailServerConfigurationValue extends BaseModel
{
    
    protected $table = "email_server_configuration_values";
    
    const EMAIL_SERVER_ID = "email_server_id";
    const CONFIGURATION_FIELD_ID = "configuration_field_id";
    const FIELD_VALUE = "field_value";
    protected $fillable = [
        self::EMAIL_SERVER_ID,
        self::CONFIGURATION_FIELD_ID,
        self::FIELD_VALUE,
    ];
}