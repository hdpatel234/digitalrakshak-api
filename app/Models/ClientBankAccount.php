<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientBankAccount extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_bank_accounts";
    
    const CLIENT_ID = "client_id";
    const ACCOUNT_NAME = "account_name";
    const BANK_NAME = "bank_name";
    const ACCOUNT_NUMBER = "account_number";
    const ACCOUNT_TYPE = "account_type";
    const IFSC_CODE = "ifsc_code";
    const SWIFT_CODE = "swift_code";
    const BRANCH_NAME = "branch_name";
    const BRANCH_ADDRESS = "branch_address";
    const UPI_ID = "upi_id";
    const QR_CODE = "qr_code";
    const IS_PRIMARY = "is_primary";
    const IS_ACTIVE = "is_active";
    const DISPLAY_ORDER = "display_order";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::ACCOUNT_NAME,
        self::BANK_NAME,
        self::ACCOUNT_NUMBER,
        self::ACCOUNT_TYPE,
        self::IFSC_CODE,
        self::SWIFT_CODE,
        self::BRANCH_NAME,
        self::BRANCH_ADDRESS,
        self::UPI_ID,
        self::QR_CODE,
        self::IS_PRIMARY,
        self::IS_ACTIVE,
        self::DISPLAY_ORDER,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}
