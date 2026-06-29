<?php

namespace App\Repositories;

use App\Models\SupportPriority;

class SupportPriorityRepository extends BaseRepository
{
    public function __construct(SupportPriority $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function name()
    {
        return SupportPriority::NAME;
    }

    public function status()
    {
        return SupportPriority::STATUS;
    }

    public function createdBy()
    {
        return SupportPriority::CREATED_BY;
    }

    public function updatedBy()
    {
        return SupportPriority::UPDATED_BY;
    }

    public function deletedBy()
    {
        return SupportPriority::DELETED_BY;
    }

    // functions
    public function getActivePriorities()
    {
        return $this->model->where($this->status(), 1)->get();
    }
}
