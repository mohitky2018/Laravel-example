<?php

declare(strict_types=1);

namespace App\Domains\Order\Events;

readonly class OrderCreated
{
    public function __construct(
        public int $userId,
        public float $totalAmount,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
