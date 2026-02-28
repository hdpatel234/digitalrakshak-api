<?php

namespace App\Services;

use App\Repositories\CandidateRepository;

class CandidateService extends BaseService
{
    public function __construct(CandidateRepository $repository)
    {
        parent::__construct($repository);
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function firstName()
    {
        return $this->repository->firstName();
    }

    public function lastName()
    {
        return $this->repository->lastName();
    }

    public function email()
    {
        return $this->repository->email();
    }

    public function phone()
    {
        return $this->repository->phone();
    }

    public function alternatePhone()
    {
        return $this->repository->alternatePhone();
    }

    public function address()
    {
        return $this->repository->address();
    }

    public function countryId()
    {
        return $this->repository->countryId();
    }
    public function stateId()
    {
        return $this->repository->stateId();
    }
    public function cityId()
    {
        return $this->repository->cityId();
    }
    public function locationVerifed()
    {
        return $this->repository->locationVerifed();
    }

    public function locationVerifiedAt()
    {
        return $this->repository->locationVerifiedAt();
    }

    public function city()
    {
        return $this->repository->city();
    }

    public function state()
    {
        return $this->repository->state();
    }

    public function pincode()
    {
        return $this->repository->pincode();
    }

    public function country()
    {
        return $this->repository->country();
    }

    public function dateOfBirth()
    {
        return $this->repository->dateOfBirth();
    }

    public function gender()
    {
        return $this->repository->gender();
    }

    public function source()
    {
        return $this->repository->source();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function invitationSentAt()
    {
        return $this->repository->invitationSentAt();
    }

    public function invitationAcceptedAt()
    {
        return $this->repository->invitationAcceptedAt();
    }

    public function lastOrderId()
    {
        return $this->repository->lastOrderId();
    }

    public function totalOrders()
    {
        return $this->repository->totalOrders();
    }

    public function totalOrderValue()
    {
        return $this->repository->totalOrderValue();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    // functions
}
