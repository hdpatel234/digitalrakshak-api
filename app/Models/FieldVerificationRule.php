<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FieldVerificationRule extends BaseModel
{
    use SoftDeletes;

    protected $table = "field_verification_rules";

    const FIELD_ID = "field_id";
    const RULE_NAME = "rule_name";
    const RULE_TYPE = "rule_type";
    const RULE_CONFIG = "rule_config";
    const PRIORITY = "priority";
    const FAILURE_ACTION = "failure_action";
    protected $fillable = [
        self::FIELD_ID,
        self::RULE_NAME,
        self::RULE_TYPE,
        self::RULE_CONFIG,
        self::PRIORITY,
        self::FAILURE_ACTION,
        self::STATUS,
    ];
}
