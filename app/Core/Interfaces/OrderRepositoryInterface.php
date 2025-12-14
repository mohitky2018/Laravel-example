<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\DTOs\OrderData;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Repository interface for Order entity operations.
 * 
 * Defines the contract for order persistence operations,
 * following the Repository pattern for data access abstraction.
 */
interface OrderRepositoryInterface
{
    /**
     * Retrieve all orders.
     *
     * @return Collection<int, Order>
     */
    public function getAll(): Collection;

    /**
     * Retrieve paginated orders.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Retrieve orders for a specific user.
     *
     * @param int $userId
     * @return Collection<int, Order>
     */
    public function getByUserId(int $userId): Collection;

    /**
     * Find an order by its ID.
     *
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order;

    /**
     * Find an order by its ID or throw an exception.
     *
     * @param int $id
     * @return Order
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Order;

    /**
     * Create a new order with items.
     *
     * @param OrderData $data
     * @return Order
     */
    public function create(OrderData $data): Order;

    /**
     * Update an order's status.
     *
     * @param int $id
     * @param string $status
     * @return Order
     */
    public function updateStatus(int $id, string $status): Order;

    /**
     * Delete an order by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
