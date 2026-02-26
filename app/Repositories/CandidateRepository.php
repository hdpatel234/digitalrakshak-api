<?php

namespace App\Repositories;

use App\Models\Candidate;

class CandidateRepository extends BaseRepository
{
    public function __construct(Candidate $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function firstName()
    {
        return $this->model::FIRST_NAME;
    }

    public function lastName()
    {
        return $this->model::LAST_NAME;
    }

    public function email()
    {
        return $this->model::EMAIL;
    }

    public function phone()
    {
        return $this->model::PHONE;
    }

    public function alternatePhone()
    {
        return $this->model::ALTERNATE_PHONE;
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

    public function dateOfBirth()
    {
        return $this->model::DATE_OF_BIRTH;
    }

    public function gender()
    {
        return $this->model::GENDER;
    }

    public function source()
    {
        return $this->model::SOURCE;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function invitationSentAt()
    {
        return $this->model::INVITATION_SENT_AT;
    }

    public function invitationAcceptedAt()
    {
        return $this->model::INVITATION_ACCEPTED_AT;
    }

    public function lastOrderId()
    {
        return $this->model::LAST_ORDER_ID;
    }

    public function totalOrders()
    {
        return $this->model::TOTAL_ORDERS;
    }

    public function totalOrderValue()
    {
        return $this->model::TOTAL_ORDER_VALUE;
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