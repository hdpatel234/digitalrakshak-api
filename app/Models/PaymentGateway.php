<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends BaseModel
{
    use SoftDeletes;
    protected $table = "payment_gateways";
    const GATEWAY_NAME = "gateway_name";
    const GATEWAY_CODE = "gateway_code";
    const PROVIDER_COMPANY = "provider_company";
    const WEBSITE = "website";
    const DESCRIPTION = "description";
    const LOGO = "logo";
    const SUPPORTED_METHODS = "supported_methods";
    const IS_DEFAULT = "is_default";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::GATEWAY_NAME,
        self::GATEWAY_CODE,
        self::PROVIDER_COMPANY,
        self::WEBSITE,
        self::DESCRIPTION,
        self::LOGO,
        self::SUPPORTED_METHODS,
        self::IS_DEFAULT,
        self::DISPLAY_ORDER,
        self::STATUS
    ];

    public function gatewayConfigs(): HasMany
    {
        return $this->hasMany(PaymentGatewayConfig::class, PaymentGatewayConfig::GATEWAY_ID);
    }
}
