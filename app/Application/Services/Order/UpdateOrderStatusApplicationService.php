<?php

declare(strict_types=1);

namespace App\Application\Services\Order;

use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Order\Exceptions\OrderNotFoundException;
use App\Domains\Order\Repositories\OrderRepositoryInterface;
use App\Domains\Order\ValueObjects\OrderStatus;

class UpdateOrderStatusApplicationService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function execute(int $orderId, string $status): OrderAggregate
    {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }

        $newStatus = new OrderStatus($status);
        $order->changeStatus($newStatus);

        return $this->orderRepository->save($order);
    }
}
