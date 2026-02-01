<?php

declare(strict_types=1);

namespace App\Domains\Product\ValueObjects;

readonly class ProductStatus
{
    private bool $isActive;

    public function __construct(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    public static function active(): self
    {
        return new self(true);
    }

    public static function inactive(): self
    {
        return new self(false);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function equals(ProductStatus $other): bool
    {
        return $this->isActive === $other->isActive;
    }

    public function __toString(): string
    {
        return $this->isActive ? 'active' : 'inactive';
    }
}
