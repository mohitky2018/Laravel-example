<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Core\DTOs\ProductData;
use App\Domains\Product\Entities\ProductEntity;
use App\Domains\Product\Exceptions\ProductNotFoundException;
use App\Domains\Product\Repositories\ProductRepositoryInterface;
use App\Domains\Product\ValueObjects\Money;

class UpdateProductApplicationService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function execute(int $productId, ProductData $data): ProductEntity
    {
        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            throw new ProductNotFoundException($productId);
        }

        $product->changeName($data->name);
        $product->changeDescription($data->description);

        $price = new Money($data->price);
        $product->changePrice($price);

        if ($data->isActive) {
            $product->activate();
        } else {
            $product->deactivate();
        }

        return $this->productRepository->save($product);
    }
}
