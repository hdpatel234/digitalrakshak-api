<?php

namespace App\Repositories;

use App\Models\CandidateServiceDatum;

class CandidateServiceDatumRepository extends BaseRepository
{
    public function __construct(CandidateServiceDatum $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateServiceId()
    {
        return $this->model::CANDIDATE_SERVICE_ID;
    }

    public function fieldId()
    {
        return $this->model::FIELD_ID;
    }

    public function fieldValue()
    {
        return $this->model::FIELD_VALUE;
    }

    public function isVerified()
    {
        return $this->model::IS_VERIFIED;
    }

    public function verifiedAt()
    {
        return $this->model::VERIFIED_AT;
    }

    public function verifiedBy()
    {
        return $this->model::VERIFIED_BY;
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