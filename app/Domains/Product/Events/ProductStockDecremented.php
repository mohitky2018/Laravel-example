<?php

declare(strict_types=1);

namespace App\Domains\Product\Events;

readonly class ProductStockDecremented
{
    public function __construct(
        public ?int $productId,
        public int $quantity,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
