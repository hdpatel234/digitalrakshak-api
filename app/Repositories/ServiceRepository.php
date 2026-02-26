<?php

namespace App\Repositories;

use App\Models\Service;

class ServiceRepository extends BaseRepository
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceName()
    {
        return $this->model::SERVICE_NAME;
    }

    public function serviceCode()
    {
        return $this->model::SERVICE_CODE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function basePrice()
    {
        return $this->model::BASE_PRICE;
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