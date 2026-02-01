<?php

declare(strict_types=1);

namespace App\Domains\Order\Events;

readonly class OrderStatusChanged
{
    public function __construct(
        public ?int $orderId,
        public string $oldStatus,
        public string $newStatus,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
