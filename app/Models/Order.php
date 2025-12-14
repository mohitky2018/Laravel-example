<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Order Model
 *
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property float $total_amount
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $items
 */
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user who placed this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get available order statuses.
     *
     * @return array<string>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Calculate and update the total amount based on items.
     */
    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->save();
    }
}
