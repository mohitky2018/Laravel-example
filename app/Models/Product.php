<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Product Model
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $stock
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $orderItems
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all order items that include this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if product has sufficient stock.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Decrease stock by the given quantity.
     */
    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    /**
     * Increase stock by the given quantity.
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}
