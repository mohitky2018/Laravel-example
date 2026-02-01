<?php

declare(strict_types=1);

namespace App\Domains\Order\Services;

use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Product\ValueObjects\Money;

class OrderTotalCalculator
{
    public function calculate(OrderAggregate $order): Money
    {
        return $order->calculateTotal();
    }
}
