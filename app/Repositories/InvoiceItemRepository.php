<?php

namespace App\Repositories;

use App\Models\InvoiceItem;

class InvoiceItemRepository extends BaseRepository
{
    public function __construct(InvoiceItem $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function invoiceId()
    {
        return $this->model::INVOICE_ID;
    }

    public function orderItemId()
    {
        return $this->model::ORDER_ITEM_ID;
    }

    public function itemType()
    {
        return $this->model::ITEM_TYPE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function quantity()
    {
        return $this->model::QUANTITY;
    }

    public function unitPrice()
    {
        return $this->model::UNIT_PRICE;
    }

    public function discountAmount()
    {
        return $this->model::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return $this->model::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return $this->model::TAX_PERCENTAGE;
    }

    public function totalPrice()
    {
        return $this->model::TOTAL_PRICE;
    }

    public function externalItemId()
    {
        return $this->model::EXTERNAL_ITEM_ID;
    }

    public function itemData()
    {
        return $this->model::ITEM_DATA;
    }
    // functions
}