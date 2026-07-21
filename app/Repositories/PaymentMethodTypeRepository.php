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
        return PaymentMethodType::METHOD_NAME;
    }

    public function methodCode()
    {
        return PaymentMethodType::METHOD_CODE;
    }

    public function category()
    {
        return PaymentMethodType::CATEGORY;
    }

    public function icon()
    {
        return PaymentMethodType::ICON;
    }

    public function description()
    {
        return PaymentMethodType::DESCRIPTION;
    }

    public function configurationSchema()
    {
        return PaymentMethodType::CONFIGURATION_SCHEMA;
    }

    public function isActive()
    {
        return PaymentMethodType::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return PaymentMethodType::DISPLAY_ORDER;
    }
    // functions
}
