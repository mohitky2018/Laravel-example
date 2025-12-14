<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\DTOs\ProductData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Repository interface for Product entity operations.
 * 
 * Defines the contract for product persistence operations,
 * following the Repository pattern for data access abstraction.
 */
interface ProductRepositoryInterface
{
    /**
     * Retrieve all products.
     *
     * @return Collection<int, Product>
     */
    public function getAll(): Collection;

    /**
     * Retrieve all active products.
     *
     * @return Collection<int, Product>
     */
    public function getActive(): Collection;

    /**
     * Retrieve paginated products.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a product by its ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product;

    /**
     * Find a product by its ID or throw an exception.
     *
     * @param int $id
     * @return Product
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Product;

    /**
     * Create a new product.
     *
     * @param ProductData $data
     * @return Product
     */
    public function create(ProductData $data): Product;

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param ProductData $data
     * @return Product
     */
    public function update(int $id, ProductData $data): Product;

    /**
     * Delete a product by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Update stock for a product.
     *
     * @param int $id
     * @param int $quantity (positive to add, negative to subtract)
     * @return Product
     */
    public function updateStock(int $id, int $quantity): Product;
}
