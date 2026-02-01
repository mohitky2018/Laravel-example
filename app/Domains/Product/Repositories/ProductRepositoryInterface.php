<?php

declare(strict_types=1);

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\ProductEntity;

interface ProductRepositoryInterface
{
    public function save(ProductEntity $product): ProductEntity;

    public function findById(int $id): ?ProductEntity;

    public function delete(int $id): bool;

    public function getAll(): array;

    public function getAllActive(): array;
}
