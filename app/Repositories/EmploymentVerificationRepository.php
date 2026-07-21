<?php

namespace App\Repositories;

use App\Models\EmploymentVerification;

class EmploymentVerificationRepository extends BaseRepository
{
    public function __construct(EmploymentVerification $model)
    {
        parent::__construct($model);
    }

    public function candidateServiceId()
    {
        return EmploymentVerification::order_item_id;
    }

    public function token()
    {
        return EmploymentVerification::TOKEN;
    }

    public function companyEmail()
    {
        return EmploymentVerification::COMPANY_EMAIL;
    }

    public function candidateData()
    {
        return EmploymentVerification::CANDIDATE_DATA;
    }

    public function status()
    {
        return EmploymentVerification::STATUS;
    }

    public function remarks()
    {
        return EmploymentVerification::REMARKS;
    }

    public function verifiedAt()
    {
        return EmploymentVerification::VERIFIED_AT;
    }
}
