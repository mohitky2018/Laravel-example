<?php

declare(strict_types=1);

namespace App\Domains\Order\ValueObjects;

use InvalidArgumentException;

readonly class OrderStatus
{
    public const PENDING = 'pending';

    public const PROCESSING = 'processing';

    public const COMPLETED = 'completed';

    public const CANCELLED = 'cancelled';

    private string $value;

    public function __construct(string $status)
    {
        if (! in_array($status, self::getValidStatuses(), true)) {
            throw new InvalidArgumentException(
                "Invalid order status: {$status}. Valid statuses are: ".implode(', ', self::getValidStatuses())
            );
        }

        $this->value = $status;
    }

    public static function getValidStatuses(): array
    {
        return [self::PENDING, self::PROCESSING, self::COMPLETED, self::CANCELLED];
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->value === self::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->value === self::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }

    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return match ($this->value) {
            self::PENDING => in_array($newStatus->value, [self::PROCESSING, self::CANCELLED], true),
            self::PROCESSING => in_array($newStatus->value, [self::COMPLETED, self::CANCELLED], true),
            self::COMPLETED, self::CANCELLED => false,
        };
    }

    public function equals(OrderStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
