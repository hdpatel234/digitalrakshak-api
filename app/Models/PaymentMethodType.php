<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethodType extends BaseModel
{
    use SoftDeletes;
    protected $table = "payment_method_types";
    const METHOD_NAME = "method_name";
    const METHOD_CODE = "method_code";
    const CATEGORY = "category";
    const ICON = "icon";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::METHOD_NAME,
        self::METHOD_CODE,
        self::CATEGORY,
        self::ICON,
        self::DISPLAY_ORDER,
        self::STATUS
    ];
}
