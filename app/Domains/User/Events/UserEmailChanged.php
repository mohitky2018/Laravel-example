<?php

declare(strict_types=1);

namespace App\Domains\User\Events;

readonly class UserEmailChanged
{
    public function __construct(
        public ?int $userId,
        public string $oldEmail,
        public string $newEmail,
        public \DateTimeImmutable $occurredAt = new \DateTimeImmutable
    ) {}
}
