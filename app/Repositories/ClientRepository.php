<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository extends BaseRepository
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function companyName()
    {
        return $this->model::COMPANY_NAME;
    }

    public function contactPerson()
    {
        return $this->model::CONTACT_PERSON;
    }

    public function email()
    {
        return $this->model::EMAIL;
    }

    public function phone()
    {
        return $this->model::PHONE;
    }

    public function gstNumber()
    {
        return $this->model::GST_NUMBER;
    }

    public function panNumber()
    {
        return $this->model::PAN_NUMBER;
    }

    public function address()
    {
        return $this->model::ADDRESS;
    }

    public function city()
    {
        return $this->model::CITY;
    }

    public function state()
    {
        return $this->model::STATE;
    }

    public function pincode()
    {
        return $this->model::PINCODE;
    }

    public function country()
    {
        return $this->model::COUNTRY;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function creditLimit()
    {
        return $this->model::CREDIT_LIMIT;
    }

    public function creditBalance()
    {
        return $this->model::CREDIT_BALANCE;
    }

    public function paymentTerms()
    {
        return $this->model::PAYMENT_TERMS;
    }

    public function defaultBillingConfigId()
    {
        return $this->model::DEFAULT_BILLING_CONFIG_ID;
    }

    public function defaultSupportConfigId()
    {
        return $this->model::DEFAULT_SUPPORT_CONFIG_ID;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}