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
        return InvoiceItem::INVOICE_ID;
    }

    public function orderItemId()
    {
        return InvoiceItem::ORDER_ITEM_ID;
    }

    public function itemType()
    {
        return InvoiceItem::ITEM_TYPE;
    }

    public function description()
    {
        return InvoiceItem::DESCRIPTION;
    }

    public function quantity()
    {
        return InvoiceItem::QUANTITY;
    }

    public function unitPrice()
    {
        return InvoiceItem::UNIT_PRICE;
    }

    public function discountAmount()
    {
        return InvoiceItem::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return InvoiceItem::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return InvoiceItem::TAX_PERCENTAGE;
    }

    public function totalPrice()
    {
        return InvoiceItem::TOTAL_PRICE;
    }

    public function externalItemId()
    {
        return InvoiceItem::EXTERNAL_ITEM_ID;
    }

    public function itemData()
    {
        return InvoiceItem::ITEM_DATA;
    }
    // functions
}