<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends BaseModel
{
    
    protected $table = "clients";
    
    const COMPANY_NAME = "company_name";
    const CONTACT_PERSON = "contact_person";
    const EMAIL = "email";
    const PHONE = "phone";
    const GST_NUMBER = "gst_number";
    const PAN_NUMBER = "pan_number";
    const ADDRESS = "address";
    const CITY = "city";
    const STATE = "state";
    const PINCODE = "pincode";
    const COUNTRY = "country";
    const CURRENCY = "currency";
    const CREDIT_LIMIT = "credit_limit";
    const CREDIT_BALANCE = "credit_balance";
    const PAYMENT_TERMS = "payment_terms";
    const DEFAULT_BILLING_CONFIG_ID = "default_billing_config_id";
    const DEFAULT_SUPPORT_CONFIG_ID = "default_support_config_id";
    const DEFAULT_DOCUMENT_CONFIG_ID = "default_document_config_id";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::COMPANY_NAME,
        self::CONTACT_PERSON,
        self::EMAIL,
        self::PHONE,
        self::GST_NUMBER,
        self::PAN_NUMBER,
        self::ADDRESS,
        self::CITY,
        self::STATE,
        self::PINCODE,
        self::COUNTRY,
        self::CURRENCY,
        self::CREDIT_LIMIT,
        self::CREDIT_BALANCE,
        self::PAYMENT_TERMS,
        self::DEFAULT_BILLING_CONFIG_ID,
        self::DEFAULT_SUPPORT_CONFIG_ID,
        self::DEFAULT_DOCUMENT_CONFIG_ID,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}