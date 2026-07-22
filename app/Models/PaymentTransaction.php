<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends BaseModel
{
    use SoftDeletes;

    protected $table = "payment_transactions";

    const CLIENT_ID = "client_id";
    const ORDER_ID = "order_id";
    const INVOICE_ID = "invoice_id";
    const GATEWAY_CONFIG_ID = "gateway_config_id";
    const METHOD_TYPE_ID = "method_type_id";
    const AMOUNT = "amount";
    const CURRENCY = "currency";
    const TAX_AMOUNT = "tax_amount";
    const FEE_AMOUNT = "fee_amount";
    const NET_AMOUNT = "net_amount";
    const GATEWAY_TRANSACTION_ID = "gateway_transaction_id";
    const GATEWAY_ORDER_ID = "gateway_order_id";
    const GATEWAY_PAYMENT_ID = "gateway_payment_id";
    const PAYMENT_DETAILS = "payment_details";
    const STATUS = "status";
    const PAYMENT_STATUS = "payment_status";
    const INITIATED_AT = "initiated_at";
    const AUTHORIZED_AT = "authorized_at";
    const CAPTURED_AT = "captured_at";
    const SUCCESS_AT = "success_at";
    const FAILED_AT = "failed_at";
    const REFUNDED_AT = "refunded_at";
    const GATEWAY_REQUEST = "gateway_request";
    const GATEWAY_RESPONSE = "gateway_response";
    const ERROR_CODE = "error_code";
    const ERROR_MESSAGE = "error_message";
    const IP_ADDRESS = "ip_address";
    const USER_AGENT = "user_agent";
    protected $fillable = [
        self::CLIENT_ID,
        self::ORDER_ID,
        self::INVOICE_ID,
        self::GATEWAY_CONFIG_ID,
        self::METHOD_TYPE_ID,
        self::AMOUNT,
        self::CURRENCY,
        self::TAX_AMOUNT,
        self::FEE_AMOUNT,
        self::NET_AMOUNT,
        self::GATEWAY_TRANSACTION_ID,
        self::GATEWAY_ORDER_ID,
        self::GATEWAY_PAYMENT_ID,
        self::PAYMENT_DETAILS,
        self::STATUS,
        self::PAYMENT_STATUS,
        self::INITIATED_AT,
        self::AUTHORIZED_AT,
        self::CAPTURED_AT,
        self::SUCCESS_AT,
        self::FAILED_AT,
        self::REFUNDED_AT,
        self::GATEWAY_REQUEST,
        self::GATEWAY_RESPONSE,
        self::ERROR_CODE,
        self::ERROR_MESSAGE,
        self::IP_ADDRESS,
        self::USER_AGENT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
    public function client()
    {
        return $this->belongsTo(Client::class, self::CLIENT_ID);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, self::ORDER_ID);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, self::INVOICE_ID);
    }

    public function gatewayConfig()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, self::GATEWAY_CONFIG_ID);
    }

    public function methodType()
    {
        return $this->belongsTo(PaymentMethodType::class, self::METHOD_TYPE_ID);
    }
}
