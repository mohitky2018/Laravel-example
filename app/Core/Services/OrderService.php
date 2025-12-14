<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\DTOs\OrderData;
use App\Core\Interfaces\OrderRepositoryInterface;
use App\Core\Interfaces\ProductRepositoryInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Service class for Order business logic.
 * 
 * Handles all order-related business operations including
 * validation, stock checking, and delegation to repository.
 */
class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Get all orders.
     *
     * @return Collection<int, Order>
     */
    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAll();
    }

    /**
     * Get paginated orders.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getPaginated($perPage);
    }

    /**
     * Get orders for a specific user.
     *
     * @param int $userId
     * @return Collection<int, Order>
     */
    public function getOrdersByUser(int $userId): Collection
    {
        return $this->orderRepository->getByUserId($userId);
    }

    /**
     * Find an order by ID.
     *
     * @param int $id
     * @return Order|null
     */
    public function findOrder(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    /**
     * Find an order by ID or throw an exception.
     *
     * @param int $id
     * @return Order
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrderOrFail(int $id): Order
    {
        return $this->orderRepository->findByIdOrFail($id);
    }

    /**
     * Create a new order.
     * 
     * Validates stock availability before creating the order.
     *
     * @param OrderData $data
     * @return Order
     * @throws \InvalidArgumentException if insufficient stock
     */
    public function createOrder(OrderData $data): Order
    {
        // Validate stock availability for all items
        $this->validateStockAvailability($data);

        return $this->orderRepository->create($data);
    }

    /**
     * Update order status.
     *
     * @param int $id
     * @param string $status
     * @return Order
     * @throws \InvalidArgumentException if invalid status
     */
    public function updateOrderStatus(int $id, string $status): Order
    {
        $validStatuses = Order::getStatuses();

        if (!in_array($status, $validStatuses, true)) {
            throw new \InvalidArgumentException(
                "Invalid status. Must be one of: " . implode(', ', $validStatuses)
            );
        }

        return $this->orderRepository->updateStatus($id, $status);
    }

    /**
     * Cancel an order.
     * 
     * Updates status to cancelled and restores stock.
     *
     * @param int $id
     * @return Order
     */
    public function cancelOrder(int $id): Order
    {
        return $this->updateOrderStatus($id, Order::STATUS_CANCELLED);
    }

    /**
     * Delete an order.
     *
     * @param int $id
     * @return bool
     */
    public function deleteOrder(int $id): bool
    {
        return $this->orderRepository->delete($id);
    }

    /**
     * Validate stock availability for order items.
     *
     * @param OrderData $data
     * @throws \InvalidArgumentException if insufficient stock
     */
    private function validateStockAvailability(OrderData $data): void
    {
        foreach ($data->items as $item) {
            $product = $this->productRepository->findByIdOrFail($item->productId);

            if (!$product->hasStock($item->quantity)) {
                throw new \InvalidArgumentException(
                    "Insufficient stock for product '{$product->name}'. Available: {$product->stock}, Requested: {$item->quantity}"
                );
            }
        }
    }
}
