<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingServiceMapping extends BaseModel
{
    protected $table = 'billing_service_mappings';

    const BILLING_PLATFORM_ID = 'billing_platform_id';
    const PACKAGE_ID = 'package_id';
    const EXTERNAL_SERVICE_ID = 'external_service_id';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const STATUS = 'status';

    protected $fillable = [
        self::BILLING_PLATFORM_ID,
        self::PACKAGE_ID,
        self::EXTERNAL_SERVICE_ID,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::STATUS,
    ];

    public function billingPlatform(): BelongsTo
    {
        return $this->belongsTo(BillingPlatform::class, self::BILLING_PLATFORM_ID);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, self::PACKAGE_ID);
    }
}
