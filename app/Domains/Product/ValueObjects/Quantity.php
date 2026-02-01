<?php

declare(strict_types=1);

namespace App\Domains\Product\ValueObjects;

use InvalidArgumentException;

readonly class Quantity
{
    private int $value;

    public function __construct(int $quantity)
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative');
        }

        $this->value = $quantity;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function add(Quantity $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(Quantity $other): self
    {
        if ($this->value < $other->value) {
            throw new InvalidArgumentException('Cannot subtract to negative quantity');
        }

        return new self($this->value - $other->value);
    }

    public function isGreaterThan(Quantity $other): bool
    {
        return $this->value > $other->value;
    }

    public function isGreaterThanOrEqual(Quantity $other): bool
    {
        return $this->value >= $other->value;
    }

    public function isLessThan(Quantity $other): bool
    {
        return $this->value < $other->value;
    }

    public function isZero(): bool
    {
        return $this->value === 0;
    }

    public function equals(Quantity $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
