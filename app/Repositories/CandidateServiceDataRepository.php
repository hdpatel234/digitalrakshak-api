<?php

namespace App\Repositories;

use App\Models\CandidateServiceData;

class CandidateServiceDataRepository extends BaseRepository
{
    public function __construct(CandidateServiceData $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateServiceId()
    {
        return CandidateServiceData::CANDIDATE_SERVICE_ID;
    }

    public function fieldId()
    {
        return CandidateServiceData::FIELD_ID;
    }

    public function fieldValue()
    {
        return CandidateServiceData::FIELD_VALUE;
    }

    public function documentId()
    {
        return CandidateServiceData::DOCUMENT_ID;
    }

    public function isVerified()
    {
        return CandidateServiceData::IS_VERIFIED;
    }

    public function verifiedAt()
    {
        return CandidateServiceData::VERIFIED_AT;
    }

    public function verifiedBy()
    {
        return CandidateServiceData::VERIFIED_BY;
    }

    public function status()
    {
        return CandidateServiceData::STATUS;
    }

    public function createdBy()
    {
        return CandidateServiceData::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateServiceData::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateServiceData::DELETED_BY;
    }
    // functions
}