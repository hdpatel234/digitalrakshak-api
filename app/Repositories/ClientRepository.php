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
        return Client::COMPANY_NAME;
    }

    public function contactPerson()
    {
        return Client::CONTACT_PERSON;
    }

    public function email()
    {
        return Client::EMAIL;
    }

    public function phone()
    {
        return Client::PHONE;
    }

    public function gstNumber()
    {
        return Client::GST_NUMBER;
    }

    public function panNumber()
    {
        return Client::PAN_NUMBER;
    }

    public function address()
    {
        return Client::ADDRESS;
    }

    public function countryID()
    {
        return Client::COUNTRY_ID;
    }

    public function stateID()
    {
        return Client::STATE_ID;
    }

    public function cityId()
    {
        return Client::CITY_ID;
    }

    public function city()
    {
        return Client::CITY;
    }

    public function state()
    {
        return Client::STATE;
    }

    public function pincode()
    {
        return Client::PINCODE;
    }

    public function country()
    {
        return Client::COUNTRY;
    }

    public function currency()
    {
        return Client::CURRENCY;
    }

    public function creditLimit()
    {
        return Client::CREDIT_LIMIT;
    }

    public function creditBalance()
    {
        return Client::CREDIT_BALANCE;
    }

    public function paymentTerms()
    {
        return Client::PAYMENT_TERMS;
    }

    public function defaultBillingConfigId()
    {
        return Client::DEFAULT_BILLING_CONFIG_ID;
    }

    public function defaultSupportConfigId()
    {
        return Client::DEFAULT_SUPPORT_CONFIG_ID;
    }

    public function defualtDocumentConfigId()
    {
        return Client::DEFAULT_DOCUMENT_CONFIG_ID;
    }

    public function status()
    {
        return Client::STATUS;
    }

    public function createdBy()
    {
        return Client::CREATED_BY;
    }

    public function updatedBy()
    {
        return Client::UPDATED_BY;
    }

    public function deletedBy()
    {
        return Client::DELETED_BY;
    }
    // functions
}