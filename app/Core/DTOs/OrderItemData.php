<?php

declare(strict_types=1);

namespace App\Core\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for OrderItem data.
 * 
 * Provides a strictly typed, immutable structure for transferring
 * order item data between application layers.
 */
readonly class OrderItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public ?float $unitPrice = null,
    ) {
    }

    /**
     * Create an OrderItemData instance from an array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            quantity: (int) $data['quantity'],
            unitPrice: isset($data['unit_price']) ? (float) $data['unit_price'] : null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return array_filter([
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
        ], fn($value) => $value !== null);
    }
}
