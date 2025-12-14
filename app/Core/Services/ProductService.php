<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\DTOs\ProductData;
use App\Core\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Service class for Product business logic.
 * 
 * Handles all product-related business operations including
 * validation logic and delegation to repository.
 */
class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Get all products.
     *
     * @return Collection<int, Product>
     */
    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAll();
    }

    /**
     * Get all active products.
     *
     * @return Collection<int, Product>
     */
    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActive();
    }

    /**
     * Get paginated products.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getPaginated($perPage);
    }

    /**
     * Find a product by ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function findProduct(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    /**
     * Find a product by ID or throw an exception.
     *
     * @param int $id
     * @return Product
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findProductOrFail(int $id): Product
    {
        return $this->productRepository->findByIdOrFail($id);
    }

    /**
     * Create a new product.
     *
     * @param ProductData $data
     * @return Product
     */
    public function createProduct(ProductData $data): Product
    {
        return $this->productRepository->create($data);
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param ProductData $data
     * @return Product
     */
    public function updateProduct(int $id, ProductData $data): Product
    {
        return $this->productRepository->update($id, $data);
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    /**
     * Check if a product has sufficient stock.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function hasStock(int $productId, int $quantity): bool
    {
        $product = $this->findProductOrFail($productId);

        return $product->hasStock($quantity);
    }

    /**
     * Adjust product stock.
     *
     * @param int $id
     * @param int $quantity (positive to add, negative to subtract)
     * @return Product
     */
    public function adjustStock(int $id, int $quantity): Product
    {
        return $this->productRepository->updateStock($id, $quantity);
    }
}
