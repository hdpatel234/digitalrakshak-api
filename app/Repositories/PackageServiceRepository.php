<?php

namespace App\Repositories;

use App\Models\PackageService;

class PackageServiceRepository extends BaseRepository
{
    public function __construct(PackageService $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function packageId()
    {
        return $this->model::PACKAGE_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function priceOverride()
    {
        return $this->model::PRICE_OVERRIDE;
    }

    public function isMandatory()
    {
        return $this->model::IS_MANDATORY;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
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