<?php

namespace App\Repositories;

use App\Models\PaymentMethodType;

class PaymentMethodTypeRepository extends BaseRepository
{
    public function __construct(PaymentMethodType $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function methodName()
    {
        return $this->model::METHOD_NAME;
    }

    public function methodCode()
    {
        return $this->model::METHOD_CODE;
    }

    public function category()
    {
        return $this->model::CATEGORY;
    }

    public function icon()
    {
        return $this->model::ICON;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function configurationSchema()
    {
        return $this->model::CONFIGURATION_SCHEMA;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }
    // functions
}