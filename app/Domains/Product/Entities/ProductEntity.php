<?php

declare(strict_types=1);

namespace App\Domains\Product\Entities;

use App\Domains\Product\Events\ProductCreated;
use App\Domains\Product\Events\ProductPriceChanged;
use App\Domains\Product\Events\ProductStockDecremented;
use App\Domains\Product\Events\ProductStockIncremented;
use App\Domains\Product\Exceptions\InsufficientStockException;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\ProductStatus;
use App\Domains\Product\ValueObjects\Quantity;

class ProductEntity
{
    private array $domainEvents = [];

    public function __construct(
        private ?int $id,
        private string $name,
        private ?string $description,
        private Money $price,
        private Quantity $stock,
        private ProductStatus $status,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {}

    public static function create(
        string $name,
        ?string $description,
        Money $price,
        Quantity $stock,
        ProductStatus $status
    ): self {
        $product = new self(
            id: null,
            name: $name,
            description: $description,
            price: $price,
            stock: $stock,
            status: $status,
            createdAt: new \DateTimeImmutable,
            updatedAt: new \DateTimeImmutable
        );

        $product->recordEvent(new ProductCreated($name, $price->getAmount()));

        return $product;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function changePrice(Money $price): void
    {
        if (! $this->price->equals($price)) {
            $oldPrice = $this->price->getAmount();
            $this->price = $price;
            $this->updatedAt = new \DateTimeImmutable;
            $this->recordEvent(new ProductPriceChanged($this->id, $oldPrice, $price->getAmount()));
        }
    }

    public function getStock(): Quantity
    {
        return $this->stock;
    }

    public function hasStock(Quantity $requiredQuantity): bool
    {
        return $this->stock->isGreaterThanOrEqual($requiredQuantity);
    }

    public function decrementStock(Quantity $quantity): void
    {
        if (! $this->hasStock($quantity)) {
            throw new InsufficientStockException(
                "Insufficient stock for product '{$this->name}'. Available: {$this->stock->getValue()}, Requested: {$quantity->getValue()}"
            );
        }

        $this->stock = $this->stock->subtract($quantity);
        $this->updatedAt = new \DateTimeImmutable;
        $this->recordEvent(new ProductStockDecremented($this->id, $quantity->getValue()));
    }

    public function incrementStock(Quantity $quantity): void
    {
        $this->stock = $this->stock->add($quantity);
        $this->updatedAt = new \DateTimeImmutable;
        $this->recordEvent(new ProductStockIncremented($this->id, $quantity->getValue()));
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function activate(): void
    {
        $this->status = ProductStatus::active();
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function deactivate(): void
    {
        $this->status = ProductStatus::inactive();
        $this->updatedAt = new \DateTimeImmutable;
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
