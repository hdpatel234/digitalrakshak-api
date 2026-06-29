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
        return Package::PACKAGE_NAME;
    }

    public function packageCode()
    {
        return Package::PACKAGE_CODE;
    }

    public function description()
    {
        return Package::DESCRIPTION;
    }

    public function type()
    {
        return Package::TYPE;
    }

    public function clientId()
    {
        return Package::CLIENT_ID;
    }

    public function totalPrice()
    {
        return Package::TOTAL_PRICE;
    }

    public function discountType()
    {
        return Package::DISCOUNT_TYPE;
    }

    public function discountValue()
    {
        return Package::DISCOUNT_VALUE;
    }

    public function finalPrice()
    {
        return Package::FINAL_PRICE;
    }

    public function isActive()
    {
        return Package::IS_ACTIVE;
    }

    public function status()
    {
        return Package::STATUS;
    }

    public function createdBy()
    {
        return Package::CREATED_BY;
    }

    public function updatedBy()
    {
        return Package::UPDATED_BY;
    }

    public function deletedBy()
    {
        return Package::DELETED_BY;
    }
    // functions
}