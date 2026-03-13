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
        return $this->model::CLIENT_ID;
    }

    public function methodTypeId()
    {
        return $this->model::METHOD_TYPE_ID;
    }

    public function gatewayConfigId()
    {
        return $this->model::GATEWAY_CONFIG_ID;
    }

    public function displayName()
    {
        return $this->model::DISPLAY_NAME;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function icon()
    {
        return $this->model::ICON;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function isEnabled()
    {
        return $this->model::IS_ENABLED;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function minAmount()
    {
        return $this->model::MIN_AMOUNT;
    }

    public function maxAmount()
    {
        return $this->model::MAX_AMOUNT;
    }

    public function instructions()
    {
        return $this->model::INSTRUCTIONS;
    }
    // functions
}