<?php

namespace App\Services;

use App\Repositories\ClientBankAccountRepository;

/**
 * @property ClientBankAccountRepository $repository
 */
class ClientBankAccountService extends BaseService
{
    public function __construct(ClientBankAccountRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function accountName()
    {
        return $this->repository->accountName();
    }

    public function bankName()
    {
        return $this->repository->bankName();
    }

    public function accountNumber()
    {
        return $this->repository->accountNumber();
    }

    public function accountType()
    {
        return $this->repository->accountType();
    }

    public function ifscCode()
    {
        return $this->repository->ifscCode();
    }

    public function swiftCode()
    {
        return $this->repository->swiftCode();
    }

    public function branchName()
    {
        return $this->repository->branchName();
    }

    public function branchAddress()
    {
        return $this->repository->branchAddress();
    }

    public function upiId()
    {
        return $this->repository->upiId();
    }

    public function qrCode()
    {
        return $this->repository->qrCode();
    }

    public function isPrimary()
    {
        return $this->repository->isPrimary();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}
