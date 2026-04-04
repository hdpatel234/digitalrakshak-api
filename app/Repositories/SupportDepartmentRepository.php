<?php

namespace App\Repositories;

use App\Models\SupportDepartment;

class SupportDepartmentRepository extends BaseRepository
{
    public function __construct(SupportDepartment $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function name()
    {
        return SupportDepartment::NAME;
    }

    public function status()
    {
        return SupportDepartment::STATUS;
    }

    public function createdBy()
    {
        return SupportDepartment::CREATED_BY;
    }

    public function updatedBy()
    {
        return SupportDepartment::UPDATED_BY;
    }

    public function deletedBy()
    {
        return SupportDepartment::DELETED_BY;
    }

    // functions
    public function getActiveDepartments()
    {
        return $this->model->where($this->status(), 1)->get();
    }
}
