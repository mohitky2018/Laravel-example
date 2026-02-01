<?php

declare(strict_types=1);

namespace App\Domains\User\Entities;

use App\Domains\User\Events\UserCreated;
use App\Domains\User\Events\UserEmailChanged;
use App\Domains\User\Events\UserPasswordChanged;
use App\Domains\User\ValueObjects\Email;

class UserEntity
{
    private array $domainEvents = [];

    public function __construct(
        private ?int $id,
        private string $name,
        private Email $email,
        private string $hashedPassword,
        private ?\DateTimeImmutable $emailVerifiedAt = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {}

    public static function create(
        string $name,
        Email $email,
        string $hashedPassword
    ): self {
        $user = new self(
            id: null,
            name: $name,
            email: $email,
            hashedPassword: $hashedPassword,
            createdAt: new \DateTimeImmutable,
            updatedAt: new \DateTimeImmutable
        );

        $user->recordEvent(new UserCreated($email->getValue(), $name));

        return $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function changeEmail(Email $email): void
    {
        if (! $this->email->equals($email)) {
            $oldEmail = $this->email->getValue();
            $this->email = $email;
            $this->emailVerifiedAt = null;
            $this->updatedAt = new \DateTimeImmutable;
            $this->recordEvent(new UserEmailChanged($this->id, $oldEmail, $email->getValue()));
        }
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->hashedPassword = $hashedPassword;
        $this->updatedAt = new \DateTimeImmutable;
        $this->recordEvent(new UserPasswordChanged($this->id));
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function verifyEmail(): void
    {
        $this->emailVerifiedAt = new \DateTimeImmutable;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
