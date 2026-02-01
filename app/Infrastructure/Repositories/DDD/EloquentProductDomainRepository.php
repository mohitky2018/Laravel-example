<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\DDD;

use App\Domains\Product\Entities\ProductEntity;
use App\Domains\Product\Repositories\ProductRepositoryInterface;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\ProductStatus;
use App\Domains\Product\ValueObjects\Quantity;
use App\Models\Product;

class EloquentProductDomainRepository implements ProductRepositoryInterface
{
    public function save(ProductEntity $product): ProductEntity
    {
        if ($product->getId() === null) {
            $eloquentProduct = new Product;
        } else {
            $eloquentProduct = Product::findOrFail($product->getId());
        }

        $eloquentProduct->name = $product->getName();
        $eloquentProduct->description = $product->getDescription();
        $eloquentProduct->price = $product->getPrice()->getAmount();
        $eloquentProduct->stock = $product->getStock()->getValue();
        $eloquentProduct->is_active = $product->getStatus()->isActive();
        $eloquentProduct->save();

        $product->setId($eloquentProduct->id);

        return $product;
    }

    public function findById(int $id): ?ProductEntity
    {
        $eloquentProduct = Product::find($id);

        if ($eloquentProduct === null) {
            return null;
        }

        return $this->mapToEntity($eloquentProduct);
    }

    public function delete(int $id): bool
    {
        return Product::destroy($id) > 0;
    }

    public function getAll(): array
    {
        return Product::all()->map(fn ($product) => $this->mapToEntity($product))->toArray();
    }

    public function getAllActive(): array
    {
        return Product::where('is_active', true)
            ->get()
            ->map(fn ($product) => $this->mapToEntity($product))
            ->toArray();
    }

    private function mapToEntity(Product $eloquentProduct): ProductEntity
    {
        $entity = new ProductEntity(
            id: $eloquentProduct->id,
            name: $eloquentProduct->name,
            description: $eloquentProduct->description,
            price: new Money((float) $eloquentProduct->price),
            stock: new Quantity($eloquentProduct->stock),
            status: $eloquentProduct->is_active ? ProductStatus::active() : ProductStatus::inactive(),
            createdAt: new \DateTimeImmutable($eloquentProduct->created_at),
            updatedAt: new \DateTimeImmutable($eloquentProduct->updated_at)
        );

        return $entity;
    }
}
