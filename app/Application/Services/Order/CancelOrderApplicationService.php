<?php

declare(strict_types=1);

namespace App\Application\Services\Order;

use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Order\Exceptions\OrderNotFoundException;
use App\Domains\Order\Repositories\OrderRepositoryInterface;
use App\Domains\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CancelOrderApplicationService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function execute(int $orderId): OrderAggregate
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->orderRepository->findById($orderId);

            if ($order === null) {
                throw new OrderNotFoundException($orderId);
            }

            foreach ($order->getItems() as $item) {
                $product = $this->productRepository->findById($item->getProductId());
                if ($product !== null) {
                    $product->incrementStock($item->getQuantity());
                    $this->productRepository->save($product);
                }
            }

            $order->cancel();

            return $this->orderRepository->save($order);
        });
    }
}
