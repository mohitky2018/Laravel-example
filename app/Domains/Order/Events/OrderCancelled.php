<?php

declare(strict_types=1);

namespace App\Domains\Order\Events;

readonly class OrderCancelled
{
    public function __construct(
        public ?int $orderId,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
