<?php

declare(strict_types=1);

namespace App\Domains\Order\Aggregates;

use App\Domains\Order\Entities\OrderItemEntity;
use App\Domains\Order\Events\OrderCancelled;
use App\Domains\Order\Events\OrderCreated;
use App\Domains\Order\Events\OrderStatusChanged;
use App\Domains\Order\Exceptions\EmptyOrderException;
use App\Domains\Order\Exceptions\InvalidOrderTransitionException;
use App\Domains\Order\ValueObjects\OrderStatus;
use App\Domains\Product\ValueObjects\Money;

class OrderAggregate
{
    private array $items = [];

    private array $domainEvents = [];

    public function __construct(
        private ?int $id,
        private int $userId,
        private OrderStatus $status,
        private ?string $notes = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {}

    public static function create(
        int $userId,
        array $items,
        ?string $notes = null
    ): self {
        if (empty($items)) {
            throw new EmptyOrderException('Order must contain at least one item');
        }

        $order = new self(
            id: null,
            userId: $userId,
            status: OrderStatus::pending(),
            notes: $notes,
            createdAt: new \DateTimeImmutable,
            updatedAt: new \DateTimeImmutable
        );

        foreach ($items as $item) {
            $order->addItem($item);
        }

        $order->recordEvent(new OrderCreated($userId, $order->calculateTotal()->getAmount()));

        return $order;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function changeStatus(OrderStatus $newStatus): void
    {
        if (! $this->status->canTransitionTo($newStatus)) {
            throw new InvalidOrderTransitionException(
                "Cannot transition order from {$this->status->getValue()} to {$newStatus->getValue()}"
            );
        }

        $oldStatus = $this->status->getValue();
        $this->status = $newStatus;
        $this->updatedAt = new \DateTimeImmutable;
        $this->recordEvent(new OrderStatusChanged($this->id, $oldStatus, $newStatus->getValue()));
    }

    public function cancel(): void
    {
        $this->changeStatus(OrderStatus::cancelled());
        $this->recordEvent(new OrderCancelled($this->id));
    }

    public function process(): void
    {
        $this->changeStatus(OrderStatus::processing());
    }

    public function complete(): void
    {
        $this->changeStatus(OrderStatus::completed());
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function updateNotes(?string $notes): void
    {
        $this->notes = $notes;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function addItem(OrderItemEntity $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function calculateTotal(): Money
    {
        if (empty($this->items)) {
            return new Money(0.0);
        }

        $total = new Money(0.0);
        foreach ($this->items as $item) {
            $total = $total->add($item->getSubtotal());
        }

        return $total;
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
