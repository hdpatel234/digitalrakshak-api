<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderOutage extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "provider_outages";
    
    const PROVIDER_ID = "provider_id";
    const SERVICE_ID = "service_id";
    const OUTAGE_TYPE = "outage_type";
    const STARTED_AT = "started_at";
    const ENDED_AT = "ended_at";
    const DURATION_MINUTES = "duration_minutes";
    const AFFECTED_SERVICES = "affected_services";
    const ROOT_CAUSE = "root_cause";
    const RESOLUTION = "resolution";
    protected $fillable = [
        self::PROVIDER_ID,
        self::SERVICE_ID,
        self::OUTAGE_TYPE,
        self::STARTED_AT,
        self::ENDED_AT,
        self::DURATION_MINUTES,
        self::AFFECTED_SERVICES,
        self::ROOT_CAUSE,
        self::RESOLUTION,
    ];
}