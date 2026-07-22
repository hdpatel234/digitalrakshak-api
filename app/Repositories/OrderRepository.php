<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function orderNumber()
    {
        return Order::ORDER_NUMBER;
    }

    public function clientId()
    {
        return Order::CLIENT_ID;
    }

    public function packageId()
    {
        return Order::PACKAGE_ID;
    }

    public function subtotal()
    {
        return Order::SUBTOTAL;
    }

    public function discountAmount()
    {
        return Order::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return Order::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return Order::TAX_PERCENTAGE;
    }

    public function totalAmount()
    {
        return Order::TOTAL_AMOUNT;
    }

    public function paymentStatus()
    {
        return Order::PAYMENT_STATUS;
    }

    public function paymentMethod()
    {
        return Order::PAYMENT_METHOD;
    }

    public function paymentReference()
    {
        return Order::PAYMENT_REFERENCE;
    }

    public function notes()
    {
        return Order::NOTES;
    }

    public function orderDate()
    {
        return Order::ORDER_DATE;
    }

    public function processedAt()
    {
        return Order::PROCESSED_AT;
    }

    public function completedAt()
    {
        return Order::COMPLETED_AT;
    }

    public function cancelledAt()
    {
        return Order::CANCELLED_AT;
    }

    public function cancellationReason()
    {
        return Order::CANCELLATION_REASON;
    }

    // functions
    public function getTotalRevenue(string $paymentStatus)
    {
        return $this->query()->where($this->paymentStatus(), $paymentStatus)->sum($this->totalAmount());
    }

    public function countBetweenDates($start, $end)
    {
        return $this->query()->whereBetween($this->createdAt(), [$start, $end])->count();
    }

    public function getRecentOrders(int $limit)
    {
        return $this->query()->with(['client', 'candidates'])->orderBy($this->createdAt(), 'desc')->limit($limit)->get();
    }
}
