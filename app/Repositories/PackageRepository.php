<?php

namespace App\Repositories;

use App\Models\Package;

class PackageRepository extends BaseRepository
{
    public function __construct(Package $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function packageName()
    {
        return $this->model::PACKAGE_NAME;
    }

    public function packageCode()
    {
        return $this->model::PACKAGE_CODE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function type()
    {
        return $this->model::TYPE;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function totalPrice()
    {
        return $this->model::TOTAL_PRICE;
    }

    public function discountType()
    {
        return $this->model::DISCOUNT_TYPE;
    }

    public function discountValue()
    {
        return $this->model::DISCOUNT_VALUE;
    }

    public function finalPrice()
    {
        return $this->model::FINAL_PRICE;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
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