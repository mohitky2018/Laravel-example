<?php

declare(strict_types=1);

namespace App\Domains\Order\Entities;

use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\Quantity;

class OrderItemEntity
{
    public function __construct(
        private ?int $id,
        private int $productId,
        private string $productName,
        private Money $unitPrice,
        private Quantity $quantity
    ) {}

    public static function create(
        int $productId,
        string $productName,
        Money $unitPrice,
        Quantity $quantity
    ): self {
        return new self(
            id: null,
            productId: $productId,
            productName: $productName,
            unitPrice: $unitPrice,
            quantity: $quantity
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getSubtotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity->getValue());
    }
}
