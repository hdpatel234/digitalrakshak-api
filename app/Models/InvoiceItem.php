<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "invoice_items";
    
    const INVOICE_ID = "invoice_id";
    const ORDER_ITEM_ID = "order_item_id";
    const ITEM_TYPE = "item_type";
    const DESCRIPTION = "description";
    const QUANTITY = "quantity";
    const UNIT_PRICE = "unit_price";
    const DISCOUNT_AMOUNT = "discount_amount";
    const TAX_AMOUNT = "tax_amount";
    const TAX_PERCENTAGE = "tax_percentage";
    const TOTAL_PRICE = "total_price";
    const EXTERNAL_ITEM_ID = "external_item_id";
    const ITEM_DATA = "item_data";
    protected $fillable = [
        self::INVOICE_ID,
        self::ORDER_ITEM_ID,
        self::ITEM_TYPE,
        self::DESCRIPTION,
        self::QUANTITY,
        self::UNIT_PRICE,
        self::DISCOUNT_AMOUNT,
        self::TAX_AMOUNT,
        self::TAX_PERCENTAGE,
        self::TOTAL_PRICE,
        self::EXTERNAL_ITEM_ID,
        self::ITEM_DATA,
    ];
}
