<?php

declare(strict_types=1);

namespace App\Domains\User\ValueObjects;

use InvalidArgumentException;

readonly class UserStatus
{
    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    public const SUSPENDED = 'suspended';

    private string $value;

    public function __construct(string $status)
    {
        if (! in_array($status, self::getValidStatuses(), true)) {
            throw new InvalidArgumentException(
                "Invalid user status: {$status}. Valid statuses are: ".implode(', ', self::getValidStatuses())
            );
        }

        $this->value = $status;
    }

    public static function getValidStatuses(): array
    {
        return [self::ACTIVE, self::INACTIVE, self::SUSPENDED];
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    public static function suspended(): self
    {
        return new self(self::SUSPENDED);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    public function equals(UserStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
