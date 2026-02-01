<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\DDD;

use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Order\Entities\OrderItemEntity;
use App\Domains\Order\Repositories\OrderRepositoryInterface;
use App\Domains\Order\ValueObjects\OrderStatus;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\Quantity;
use App\Models\Order;
use App\Models\OrderItem;

class EloquentOrderDomainRepository implements OrderRepositoryInterface
{
    public function save(OrderAggregate $order): OrderAggregate
    {
        if ($order->getId() === null) {
            $eloquentOrder = new Order;
        } else {
            $eloquentOrder = Order::findOrFail($order->getId());
        }

        $eloquentOrder->user_id = $order->getUserId();
        $eloquentOrder->status = $order->getStatus()->getValue();
        $eloquentOrder->total_amount = $order->calculateTotal()->getAmount();
        $eloquentOrder->notes = $order->getNotes();
        $eloquentOrder->save();

        $order->setId($eloquentOrder->id);

        if ($order->getId() !== null) {
            OrderItem::where('order_id', $order->getId())->delete();
        }

        foreach ($order->getItems() as $item) {
            $eloquentItem = new OrderItem;
            $eloquentItem->order_id = $eloquentOrder->id;
            $eloquentItem->product_id = $item->getProductId();
            $eloquentItem->product_name = $item->getProductName();
            $eloquentItem->quantity = $item->getQuantity()->getValue();
            $eloquentItem->unit_price = $item->getUnitPrice()->getAmount();
            $eloquentItem->subtotal = $item->getSubtotal()->getAmount();
            $eloquentItem->save();

            $item->setId($eloquentItem->id);
        }

        return $order;
    }

    public function findById(int $id): ?OrderAggregate
    {
        $eloquentOrder = Order::with('items')->find($id);

        if ($eloquentOrder === null) {
            return null;
        }

        return $this->mapToAggregate($eloquentOrder);
    }

    public function findByUserId(int $userId): array
    {
        $eloquentOrders = Order::with('items')->where('user_id', $userId)->get();

        return $eloquentOrders->map(fn ($order) => $this->mapToAggregate($order))->toArray();
    }

    public function delete(int $id): bool
    {
        return Order::destroy($id) > 0;
    }

    public function getAll(): array
    {
        return Order::with('items')->get()->map(fn ($order) => $this->mapToAggregate($order))->toArray();
    }

    private function mapToAggregate(Order $eloquentOrder): OrderAggregate
    {
        $items = [];
        foreach ($eloquentOrder->items as $eloquentItem) {
            $items[] = new OrderItemEntity(
                id: $eloquentItem->id,
                productId: $eloquentItem->product_id,
                productName: $eloquentItem->product_name,
                unitPrice: new Money((float) $eloquentItem->unit_price),
                quantity: new Quantity($eloquentItem->quantity)
            );
        }

        $aggregate = new OrderAggregate(
            id: $eloquentOrder->id,
            userId: $eloquentOrder->user_id,
            status: new OrderStatus($eloquentOrder->status),
            notes: $eloquentOrder->notes,
            createdAt: new \DateTimeImmutable($eloquentOrder->created_at),
            updatedAt: new \DateTimeImmutable($eloquentOrder->updated_at)
        );

        $aggregate->setItems($items);

        return $aggregate;
    }
}
