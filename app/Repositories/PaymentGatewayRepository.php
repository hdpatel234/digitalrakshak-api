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
        return PaymentGateway::GATEWAY_NAME;
    }

    public function gatewayCode()
    {
        return PaymentGateway::GATEWAY_CODE;
    }

    public function providerCompany()
    {
        return PaymentGateway::PROVIDER_COMPANY;
    }

    public function website()
    {
        return PaymentGateway::WEBSITE;
    }

    public function description()
    {
        return PaymentGateway::DESCRIPTION;
    }

    public function logo()
    {
        return PaymentGateway::LOGO;
    }

    public function supportedMethods()
    {
        return PaymentGateway::SUPPORTED_METHODS;
    }

    public function configurationSchema()
    {
        return PaymentGateway::CONFIGURATION_SCHEMA;
    }

    public function isActive()
    {
        return PaymentGateway::IS_ACTIVE;
    }

    public function isDefault()
    {
        return PaymentGateway::IS_DEFAULT;
    }

    public function displayOrder()
    {
        return PaymentGateway::DISPLAY_ORDER;
    }
    // functions
}
