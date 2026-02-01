<?php

declare(strict_types=1);

namespace App\Domains\Product\ValueObjects;

use InvalidArgumentException;

readonly class ProductSKU
{
    private string $value;

    public function __construct(string $sku)
    {
        $sku = strtoupper(trim($sku));

        if (strlen($sku) < 3 || strlen($sku) > 50) {
            throw new InvalidArgumentException('SKU must be between 3 and 50 characters');
        }

        if (! preg_match('/^[A-Z0-9\-_]+$/', $sku)) {
            throw new InvalidArgumentException('SKU can only contain alphanumeric characters, hyphens, and underscores');
        }

        $this->value = $sku;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ProductSKU $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
