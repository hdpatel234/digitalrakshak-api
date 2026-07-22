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
        return SavedPaymentMethod::CLIENT_ID;
    }

    public function userId()
    {
        return SavedPaymentMethod::USER_ID;
    }

    public function customerId()
    {
        return SavedPaymentMethod::CUSTOMER_ID;
    }

    public function gatewayConfigId()
    {
        return SavedPaymentMethod::GATEWAY_CONFIG_ID;
    }

    public function methodTypeId()
    {
        return SavedPaymentMethod::METHOD_TYPE_ID;
    }

    public function gatewayCustomerId()
    {
        return SavedPaymentMethod::GATEWAY_CUSTOMER_ID;
    }

    public function gatewayPaymentMethodId()
    {
        return SavedPaymentMethod::GATEWAY_PAYMENT_METHOD_ID;
    }

    public function paymentToken()
    {
        return SavedPaymentMethod::PAYMENT_TOKEN;
    }

    public function displayName()
    {
        return SavedPaymentMethod::DISPLAY_NAME;
    }

    public function maskedDetails()
    {
        return SavedPaymentMethod::MASKED_DETAILS;
    }

    public function expiryMonth()
    {
        return SavedPaymentMethod::EXPIRY_MONTH;
    }

    public function expiryYear()
    {
        return SavedPaymentMethod::EXPIRY_YEAR;
    }

    public function cardHolderName()
    {
        return SavedPaymentMethod::CARD_HOLDER_NAME;
    }

    public function cardBrand()
    {
        return SavedPaymentMethod::CARD_BRAND;
    }

    public function bankName()
    {
        return SavedPaymentMethod::BANK_NAME;
    }

    public function upiId()
    {
        return SavedPaymentMethod::UPI_ID;
    }

    public function isDefault()
    {
        return SavedPaymentMethod::IS_DEFAULT;
    }

    public function lastUsedAt()
    {
        return SavedPaymentMethod::LAST_USED_AT;
    }

    public function usedCount()
    {
        return SavedPaymentMethod::USED_COUNT;
    }

    // functions
}
