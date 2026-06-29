<?php

namespace App\Repositories;

use App\Models\ClientBankAccount;

class ClientBankAccountRepository extends BaseRepository
{
    public function __construct(ClientBankAccount $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return ClientBankAccount::CLIENT_ID;
    }

    public function accountName()
    {
        return ClientBankAccount::ACCOUNT_NAME;
    }

    public function bankName()
    {
        return ClientBankAccount::BANK_NAME;
    }

    public function accountNumber()
    {
        return ClientBankAccount::ACCOUNT_NUMBER;
    }

    public function accountType()
    {
        return ClientBankAccount::ACCOUNT_TYPE;
    }

    public function ifscCode()
    {
        return ClientBankAccount::IFSC_CODE;
    }

    public function swiftCode()
    {
        return ClientBankAccount::SWIFT_CODE;
    }

    public function branchName()
    {
        return ClientBankAccount::BRANCH_NAME;
    }

    public function branchAddress()
    {
        return ClientBankAccount::BRANCH_ADDRESS;
    }

    public function upiId()
    {
        return ClientBankAccount::UPI_ID;
    }

    public function qrCode()
    {
        return ClientBankAccount::QR_CODE;
    }

    public function isPrimary()
    {
        return ClientBankAccount::IS_PRIMARY;
    }

    public function isActive()
    {
        return ClientBankAccount::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return ClientBankAccount::DISPLAY_ORDER;
    }

    public function createdBy()
    {
        return ClientBankAccount::CREATED_BY;
    }

    public function updatedBy()
    {
        return ClientBankAccount::UPDATED_BY;
    }
    // functions
}