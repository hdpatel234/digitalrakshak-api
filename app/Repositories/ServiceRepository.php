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
    public function serviceCategory()
    {
        return Service::SERVICE_CATEGORY;
    }

    public function serviceName()
    {
        return Service::SERVICE_NAME;
    }

    public function serviceCode()
    {
        return Service::SERVICE_CODE;
    }

    public function description()
    {
        return Service::DESCRIPTION;
    }

    public function basePrice()
    {
        return Service::BASE_PRICE;
    }

    public function status()
    {
        return Service::STATUS;
    }

    public function createdBy()
    {
        return Service::CREATED_BY;
    }

    public function updatedBy()
    {
        return Service::UPDATED_BY;
    }

    public function deletedBy()
    {
        return Service::DELETED_BY;
    }
    // functions
}
