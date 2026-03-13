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
        return $this->model::CLIENT_ID;
    }

    public function accountName()
    {
        return $this->model::ACCOUNT_NAME;
    }

    public function bankName()
    {
        return $this->model::BANK_NAME;
    }

    public function accountNumber()
    {
        return $this->model::ACCOUNT_NUMBER;
    }

    public function accountType()
    {
        return $this->model::ACCOUNT_TYPE;
    }

    public function ifscCode()
    {
        return $this->model::IFSC_CODE;
    }

    public function swiftCode()
    {
        return $this->model::SWIFT_CODE;
    }

    public function branchName()
    {
        return $this->model::BRANCH_NAME;
    }

    public function branchAddress()
    {
        return $this->model::BRANCH_ADDRESS;
    }

    public function upiId()
    {
        return $this->model::UPI_ID;
    }

    public function qrCode()
    {
        return $this->model::QR_CODE;
    }

    public function isPrimary()
    {
        return $this->model::IS_PRIMARY;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}