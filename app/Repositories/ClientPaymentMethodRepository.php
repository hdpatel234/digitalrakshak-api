<?php

namespace App\Repositories;

use App\Models\ClientPaymentMethod;

class ClientPaymentMethodRepository extends BaseRepository
{
    public function __construct(ClientPaymentMethod $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return ClientPaymentMethod::CLIENT_ID;
    }

    public function methodTypeId()
    {
        return ClientPaymentMethod::METHOD_TYPE_ID;
    }

    public function gatewayConfigId()
    {
        return ClientPaymentMethod::GATEWAY_CONFIG_ID;
    }

    public function displayName()
    {
        return ClientPaymentMethod::DISPLAY_NAME;
    }

    public function description()
    {
        return ClientPaymentMethod::DESCRIPTION;
    }

    public function icon()
    {
        return ClientPaymentMethod::ICON;
    }

    public function displayOrder()
    {
        return ClientPaymentMethod::DISPLAY_ORDER;
    }

    public function isEnabled()
    {
        return ClientPaymentMethod::IS_ENABLED;
    }

    public function isDefault()
    {
        return ClientPaymentMethod::IS_DEFAULT;
    }

    public function minAmount()
    {
        return ClientPaymentMethod::MIN_AMOUNT;
    }

    public function maxAmount()
    {
        return ClientPaymentMethod::MAX_AMOUNT;
    }

    public function instructions()
    {
        return ClientPaymentMethod::INSTRUCTIONS;
    }
    // functions
}
