<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Core\DTOs\OrderData;
use App\Core\DTOs\OrderItemData;
use App\Core\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of OrderRepositoryInterface.
 * 
 * Handles all database operations for Order entity using Eloquent ORM.
 */
class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly Order $model,
        private readonly Product $productModel
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model->newQuery()->with(['user', 'items.product'])->latest()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->with(['user', 'items.product'])->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->model->newQuery()
            ->with(['items.product'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Order
    {
        return $this->model->newQuery()->with(['user', 'items.product'])->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdOrFail(int $id): Order
    {
        return $this->model->newQuery()->with(['user', 'items.product'])->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(OrderData $data): Order
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;

            // Calculate total amount from items
            foreach ($data->items as $item) {
                $product = $this->productModel->newQuery()->findOrFail($item->productId);
                $unitPrice = $item->unitPrice ?? $product->price;
                $totalAmount += $unitPrice * $item->quantity;
            }

            // Create the order
            $order = $this->model->newQuery()->create([
                'user_id' => $data->userId,
                'status' => $data->status,
                'notes' => $data->notes,
                'total_amount' => $totalAmount,
            ]);

            // Create order items and update stock
            foreach ($data->items as $item) {
                $product = $this->productModel->newQuery()->findOrFail($item->productId);
                $unitPrice = $item->unitPrice ?? $product->price;
                $subtotal = $unitPrice * $item->quantity;

                $order->items()->create([
                    'product_id' => $item->productId,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Decrease product stock
                $product->decreaseStock($item->quantity);
            }

            return $order->load(['user', 'items.product']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(int $id, string $status): Order
    {
        $order = $this->findByIdOrFail($id);
        $order->update(['status' => $status]);

        return $order->fresh(['user', 'items.product']);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $order = $this->findByIdOrFail($id);

            // Restore stock for cancelled orders
            foreach ($order->items as $item) {
                $item->product->increaseStock($item->quantity);
            }

            // Items will be cascade deleted
            return (bool) $order->delete();
        });
    }
}
