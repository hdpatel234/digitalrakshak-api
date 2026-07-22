<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class VerificationWorkflowConfig extends BaseModel
{
    use SoftDeletes;
    protected $table = "verification_workflow_configs";
    
    const SERVICE_ID = "service_id";
    const WORKFLOW_NAME = "workflow_name";
    const WORKFLOW_STEPS = "workflow_steps";
    const AUTO_START = "auto_start";
    const TIMEOUT_MINUTES = "timeout_minutes";
    const ESCALATION_AFTER_MINUTES = "escalation_after_minutes";
    const ESCALATION_USER_ID = "escalation_user_id";
    const NOTIFICATION_CONFIG = "notification_config";
    protected $fillable = [
        self::SERVICE_ID,
        self::WORKFLOW_NAME,
        self::WORKFLOW_STEPS,
        self::AUTO_START,
        self::TIMEOUT_MINUTES,
        self::ESCALATION_AFTER_MINUTES,
        self::ESCALATION_USER_ID,
        self::NOTIFICATION_CONFIG,
    ];
}