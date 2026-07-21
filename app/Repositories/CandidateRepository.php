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
        return Candidate::CLIENT_ID;
    }

    public function firstName()
    {
        return Candidate::FIRST_NAME;
    }

    public function lastName()
    {
        return Candidate::LAST_NAME;
    }

    public function email()
    {
        return Candidate::EMAIL;
    }

    public function phone()
    {
        return Candidate::PHONE;
    }

    public function alternatePhone()
    {
        return Candidate::ALTERNATE_PHONE;
    }

    public function address()
    {
        return Candidate::ADDRESS;
    }
    public function countryId()
    {
        return Candidate::COUNTRY_ID;
    }
    public function stateId()
    {
        return Candidate::STATE_ID;
    }
    public function cityId()
    {
        return Candidate::CITY_ID;
    }

    public function country()
    {
        return Candidate::COUNTRY;
    }
    public function state()
    {
        return Candidate::STATE;
    }
    public function city()
    {
        return Candidate::CITY;
    }

    public function pincode()
    {
        return Candidate::PINCODE;
    }

    public function dateOfBirth()
    {
        return Candidate::DATE_OF_BIRTH;
    }

    public function gender()
    {
        return Candidate::GENDER;
    }

    public function source()
    {
        return Candidate::SOURCE;
    }

    public function status()
    {
        return Candidate::STATUS;
    }

    // functions
    public function countBetweenDates(string $start, string $end)
    {
        return $this->query()->whereBetween($this->createdAt(), [$start, $end])->count();
    }
}
