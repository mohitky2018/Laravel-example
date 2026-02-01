<?php

declare(strict_types=1);

namespace App\Application\Services\Order;

use App\Core\DTOs\OrderData;
use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Order\Entities\OrderItemEntity;
use App\Domains\Order\Repositories\OrderRepositoryInterface;
use App\Domains\Product\Repositories\ProductRepositoryInterface;
use App\Domains\Product\Services\StockValidator;
use App\Domains\Product\ValueObjects\Quantity;
use Illuminate\Support\Facades\DB;

class CreateOrderApplicationService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StockValidator $stockValidator
    ) {}

    public function execute(OrderData $data): OrderAggregate
    {
        return DB::transaction(function () use ($data) {
            $orderItems = [];

            foreach ($data->items as $itemData) {
                $product = $this->productRepository->findById($itemData->productId);

                if ($product === null) {
                    throw new \InvalidArgumentException("Product with ID {$itemData->productId} not found");
                }

                $quantity = new Quantity($itemData->quantity);
                $this->stockValidator->validate($product, $quantity);

                $product->decrementStock($quantity);
                $this->productRepository->save($product);

                $orderItem = OrderItemEntity::create(
                    productId: $product->getId(),
                    productName: $product->getName(),
                    unitPrice: $product->getPrice(),
                    quantity: $quantity
                );

                $orderItems[] = $orderItem;
            }

            $order = OrderAggregate::create(
                userId: $data->userId,
                items: $orderItems,
                notes: $data->notes
            );

            return $this->orderRepository->save($order);
        });
    }
}
