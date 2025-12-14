<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Core\DTOs\ProductData;
use App\Core\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of ProductRepositoryInterface.
 * 
 * Handles all database operations for Product entity using Eloquent ORM.
 */
class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $model
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getActive(): Collection
    {
        return $this->model->newQuery()->where('is_active', true)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Product
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdOrFail(int $id): Product
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(ProductData $data): Product
    {
        return $this->model->newQuery()->create($data->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, ProductData $data): Product
    {
        $product = $this->findByIdOrFail($id);
        $product->update($data->toArray());

        return $product->fresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $product = $this->findByIdOrFail($id);

        return (bool) $product->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function updateStock(int $id, int $quantity): Product
    {
        $product = $this->findByIdOrFail($id);

        if ($quantity > 0) {
            $product->increaseStock($quantity);
        } else {
            $product->decreaseStock(abs($quantity));
        }

        return $product->fresh();
    }
}
