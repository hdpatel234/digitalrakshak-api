<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentRefund extends BaseModel
{
    use SoftDeletes;

    protected $table = "payment_refunds";

    const TRANSACTION_ID = "transaction_id";
    const GATEWAY_REFUND_ID = "gateway_refund_id";
    const AMOUNT = "amount";
    const REASON = "reason";
    const STATUS = "status";
    const GATEWAY_REQUEST = "gateway_request";
    const GATEWAY_RESPONSE = "gateway_response";
    const ERROR_MESSAGE = "error_message";
    const INITIATED_BY = "initiated_by";
    const APPROVED_BY = "approved_by";
    const APPROVED_AT = "approved_at";
    protected $fillable = [
        self::TRANSACTION_ID,
        self::GATEWAY_REFUND_ID,
        self::AMOUNT,
        self::REASON,
        self::STATUS,
        self::GATEWAY_REQUEST,
        self::GATEWAY_RESPONSE,
        self::ERROR_MESSAGE,
        self::INITIATED_BY,
        self::APPROVED_BY,
        self::APPROVED_AT,
    ];
}
