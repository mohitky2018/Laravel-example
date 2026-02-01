<?php

declare(strict_types=1);

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Aggregates\OrderAggregate;

interface OrderRepositoryInterface
{
    public function save(OrderAggregate $order): OrderAggregate;

    public function findById(int $id): ?OrderAggregate;

    public function findByUserId(int $userId): array;

    public function delete(int $id): bool;

    public function getAll(): array;
}
