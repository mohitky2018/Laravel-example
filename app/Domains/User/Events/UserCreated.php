<?php

declare(strict_types=1);

namespace App\Domains\User\Events;

readonly class UserCreated
{
    public function __construct(
        public string $email,
        public string $name,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
