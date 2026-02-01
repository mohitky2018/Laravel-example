<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Core\DTOs\ProductData;
use App\Domains\Product\Entities\ProductEntity;
use App\Domains\Product\Repositories\ProductRepositoryInterface;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\ProductStatus;
use App\Domains\Product\ValueObjects\Quantity;

class CreateProductApplicationService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function execute(ProductData $data): ProductEntity
    {
        $price = new Money($data->price);
        $stock = new Quantity($data->stock);
        $status = $data->isActive ? ProductStatus::active() : ProductStatus::inactive();

        $product = ProductEntity::create(
            name: $data->name,
            description: $data->description,
            price: $price,
            stock: $stock,
            status: $status
        );

        return $this->productRepository->save($product);
    }
}
