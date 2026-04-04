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
        return $this->model::NAME;
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
    public function getActivePriorities()
    {
        return $this->model->where($this->status(), 1)->get();
    }
}
