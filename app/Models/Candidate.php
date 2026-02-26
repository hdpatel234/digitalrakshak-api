<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends BaseModel
{
    
    protected $table = "candidates";
    
    const CLIENT_ID = "client_id";
    const FIRST_NAME = "first_name";
    const LAST_NAME = "last_name";
    const EMAIL = "email";
    const PHONE = "phone";
    const ALTERNATE_PHONE = "alternate_phone";
    const ADDRESS = "address";
    const CITY = "city";
    const STATE = "state";
    const PINCODE = "pincode";
    const COUNTRY = "country";
    const DATE_OF_BIRTH = "date_of_birth";
    const GENDER = "gender";
    const SOURCE = "source";
    const STATUS = "status";
    const INVITATION_SENT_AT = "invitation_sent_at";
    const INVITATION_ACCEPTED_AT = "invitation_accepted_at";
    const LAST_ORDER_ID = "last_order_id";
    const TOTAL_ORDERS = "total_orders";
    const TOTAL_ORDER_VALUE = "total_order_value";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::PHONE,
        self::ALTERNATE_PHONE,
        self::ADDRESS,
        self::CITY,
        self::STATE,
        self::PINCODE,
        self::COUNTRY,
        self::DATE_OF_BIRTH,
        self::GENDER,
        self::SOURCE,
        self::STATUS,
        self::INVITATION_SENT_AT,
        self::INVITATION_ACCEPTED_AT,
        self::LAST_ORDER_ID,
        self::TOTAL_ORDERS,
        self::TOTAL_ORDER_VALUE,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}