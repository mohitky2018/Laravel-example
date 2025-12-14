<?php

declare(strict_types=1);

namespace App\Core\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for Order data.
 * 
 * Provides a strictly typed, immutable structure for transferring
 * order-related data between application layers.
 */
readonly class OrderData
{
    /**
     * @param int $userId
     * @param array<OrderItemData> $items
     * @param string $status
     * @param string|null $notes
     */
    public function __construct(
        public int $userId,
        public array $items = [],
        public string $status = 'pending',
        public ?string $notes = null,
    ) {
    }

    /**
     * Create an OrderData instance from an HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        $items = [];
        $requestItems = $request->input('items', []);

        foreach ($requestItems as $item) {
            if (!empty($item['product_id']) && !empty($item['quantity'])) {
                $items[] = OrderItemData::fromArray($item);
            }
        }

        return new self(
            userId: (int) $request->input('user_id'),
            items: $items,
            status: $request->input('status', 'pending'),
            notes: $request->input('notes'),
        );
    }

    /**
     * Create an OrderData instance from an array.
     */
    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            $items[] = OrderItemData::fromArray($item);
        }

        return new self(
            userId: (int) $data['user_id'],
            items: $items,
            status: $data['status'] ?? 'pending',
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'status' => $this->status,
            'notes' => $this->notes,
            'items' => array_map(fn(OrderItemData $item) => $item->toArray(), $this->items),
        ];
    }
}
