<?php

declare(strict_types=1);

namespace App\Domains\User\Events;

readonly class UserPasswordChanged
{
    public function __construct(
        public ?int $userId,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
