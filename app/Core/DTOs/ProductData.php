<?php

declare(strict_types=1);

namespace App\Core\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for Product data.
 * 
 * Provides a strictly typed, immutable structure for transferring
 * product-related data between application layers.
 */
readonly class ProductData
{
    public function __construct(
        public string $name,
        public float $price,
        public int $stock = 0,
        public ?string $description = null,
        public bool $isActive = true,
    ) {
    }

    /**
     * Create a ProductData instance from an HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            price: (float) $request->input('price'),
            stock: (int) $request->input('stock', 0),
            description: $request->input('description'),
            isActive: (bool) $request->input('is_active', true),
        );
    }

    /**
     * Create a ProductData instance from an array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            stock: (int) ($data['stock'] ?? 0),
            description: $data['description'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
