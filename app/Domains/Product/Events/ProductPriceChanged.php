<?php

declare(strict_types=1);

namespace App\Domains\Product\Events;

readonly class ProductPriceChanged
{
    public function __construct(
        public ?int $productId,
        public float $oldPrice,
        public float $newPrice,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
