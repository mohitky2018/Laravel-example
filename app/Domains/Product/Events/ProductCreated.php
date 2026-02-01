<?php

declare(strict_types=1);

namespace App\Domains\Product\Events;

readonly class ProductCreated
{
    public function __construct(
        public string $name,
        public float $price,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
