<?php

declare(strict_types=1);

namespace App\Domains\Product\Services;

use App\Domains\Product\Entities\ProductEntity;
use App\Domains\Product\Exceptions\InsufficientStockException;
use App\Domains\Product\ValueObjects\Quantity;

class StockValidator
{
    public function validate(ProductEntity $product, Quantity $requiredQuantity): void
    {
        if (! $product->hasStock($requiredQuantity)) {
            throw new InsufficientStockException(
                "Insufficient stock for product '{$product->getName()}'. ".
                "Available: {$product->getStock()->getValue()}, Requested: {$requiredQuantity->getValue()}"
            );
        }
    }

    public function validateMultiple(array $products, array $quantities): void
    {
        foreach ($products as $index => $product) {
            $this->validate($product, $quantities[$index]);
        }
    }
}
