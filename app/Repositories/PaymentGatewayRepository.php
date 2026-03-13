<?php

namespace App\Repositories;

use App\Models\PaymentGateway;

class PaymentGatewayRepository extends BaseRepository
{
    public function __construct(PaymentGateway $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function gatewayName()
    {
        return $this->model::GATEWAY_NAME;
    }

    public function gatewayCode()
    {
        return $this->model::GATEWAY_CODE;
    }

    public function providerCompany()
    {
        return $this->model::PROVIDER_COMPANY;
    }

    public function website()
    {
        return $this->model::WEBSITE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function logo()
    {
        return $this->model::LOGO;
    }

    public function supportedMethods()
    {
        return $this->model::SUPPORTED_METHODS;
    }

    public function configurationSchema()
    {
        return $this->model::CONFIGURATION_SCHEMA;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }
    // functions
}