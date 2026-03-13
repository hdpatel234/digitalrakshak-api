<?php

namespace App\Repositories;

use App\Models\SavedPaymentMethod;

class SavedPaymentMethodRepository extends BaseRepository
{
    public function __construct(SavedPaymentMethod $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function customerId()
    {
        return $this->model::CUSTOMER_ID;
    }

    public function gatewayConfigId()
    {
        return $this->model::GATEWAY_CONFIG_ID;
    }

    public function methodTypeId()
    {
        return $this->model::METHOD_TYPE_ID;
    }

    public function gatewayCustomerId()
    {
        return $this->model::GATEWAY_CUSTOMER_ID;
    }

    public function gatewayPaymentMethodId()
    {
        return $this->model::GATEWAY_PAYMENT_METHOD_ID;
    }

    public function paymentToken()
    {
        return $this->model::PAYMENT_TOKEN;
    }

    public function displayName()
    {
        return $this->model::DISPLAY_NAME;
    }

    public function maskedDetails()
    {
        return $this->model::MASKED_DETAILS;
    }

    public function expiryMonth()
    {
        return $this->model::EXPIRY_MONTH;
    }

    public function expiryYear()
    {
        return $this->model::EXPIRY_YEAR;
    }

    public function cardHolderName()
    {
        return $this->model::CARD_HOLDER_NAME;
    }

    public function cardBrand()
    {
        return $this->model::CARD_BRAND;
    }

    public function bankName()
    {
        return $this->model::BANK_NAME;
    }

    public function upiId()
    {
        return $this->model::UPI_ID;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
    }

    public function usedCount()
    {
        return $this->model::USED_COUNT;
    }
    // functions
}