# Domain-Driven Design Architecture Guide

## Overview

This document provides detailed guidance on the Domain-Driven Design (DDD) implementation in this Laravel application.

---

## Table of Contents

1. [Introduction](#introduction)
2. [Bounded Contexts](#bounded-contexts)
3. [Tactical Patterns](#tactical-patterns)
4. [Layer Architecture](#layer-architecture)
5. [Usage Examples](#usage-examples)
6. [Best Practices](#best-practices)
7. [Migration Guide](#migration-guide)

---

## Introduction

Domain-Driven Design is a software development approach that focuses on:

- **Complex Domain Logic**: Modeling complex business rules in code
- **Ubiquitous Language**: Shared vocabulary between developers and domain experts
- **Bounded Contexts**: Clear boundaries separating different business concerns
- **Rich Domain Models**: Business logic lives in domain objects, not services

---

## Bounded Contexts

This application is divided into three bounded contexts:

### 1. User Context

**Purpose**: Manages user identity, authentication, and profile information

**Key Components:**
- **Entity**: `UserEntity` - Represents a user with unique identity
- **Value Objects**: 
  - `Email` - Valid, normalized email address
  - `UserStatus` - User account status (active, inactive, suspended)
- **Domain Services**: 
  - `UserPasswordHasher` - Handles password hashing/verification
- **Events**:
  - `UserCreated` - New user registered
  - `UserEmailChanged` - User changed email address
  - `UserPasswordChanged` - User changed password

**Invariants:**
- Email must be valid format
- Email must be unique (enforced at database level)
- Password must be hashed before storage

### 2. Product Context

**Purpose**: Manages product catalog, pricing, and inventory

**Key Components:**
- **Entity**: `ProductEntity` - Represents a product
- **Value Objects**:
  - `Money` - Monetary amount with currency
  - `Quantity` - Non-negative integer quantity
  - `ProductSKU` - Stock Keeping Unit identifier
  - `ProductStatus` - Product availability status
- **Domain Services**:
  - `StockValidator` - Validates stock availability
- **Events**:
  - `ProductCreated` - New product added
  - `ProductStockDecremented` - Stock reduced
  - `ProductStockIncremented` - Stock increased
  - `ProductPriceChanged` - Price modified

**Invariants:**
- Stock cannot be negative
- Price cannot be negative
- Stock decrements only if sufficient quantity available

### 3. Order Context

**Purpose**: Manages order lifecycle and fulfillment

**Key Components:**
- **Aggregate**: `OrderAggregate` - Root aggregate managing order
- **Entity**: `OrderItemEntity` - Individual item in order
- **Value Objects**:
  - `OrderStatus` - Order state (pending, processing, completed, cancelled)
  - Reuses `Money` and `Quantity` from Product context
- **Domain Services**:
  - `OrderTotalCalculator` - Calculates order total
- **Events**:
  - `OrderCreated` - New order placed
  - `OrderStatusChanged` - Order status updated
  - `OrderCancelled` - Order cancelled

**Invariants:**
- Order must have at least one item
- Order status transitions must be valid
- Total amount equals sum of item subtotals
- Cannot cancel completed orders

---

## Tactical Patterns

### Value Objects

**Purpose**: Represent concepts by their value, not identity

**Examples:**

```php
// Email Value Object
$email = new Email('john@example.com');
$sameEmail = new Email('john@example.com');
$email->equals($sameEmail); // true - compared by value

// Money Value Object
$price = new Money(29.99, 'USD');
$discount = new Money(5.00, 'USD');
$finalPrice = $price->subtract($discount); // Money(24.99, 'USD')

// Quantity Value Object
$stock = new Quantity(100);
$sold = new Quantity(25);
$remaining = $stock->subtract($sold); // Quantity(75)
```

**Characteristics:**
- Immutable (readonly properties)
- Self-validating (constructor validates)
- Value equality
- Rich behavior (methods like add, subtract)

### Entities

**Purpose**: Objects with unique identity that changes over time

**Example:**

```php
// Creating a User Entity
$user = UserEntity::create(
    name: 'John Doe',
    email: new Email('john@example.com'),
    hashedPassword: $hashedPassword
);

// Entity maintains identity through changes
$user->changeName('Jane Doe');
$user->getId(); // Same ID
```

**Characteristics:**
- Unique identifier (ID)
- Mutable state
- Identity equality (compare by ID)
- Lifecycle methods (create, change, etc.)
- Records domain events

### Aggregates

**Purpose**: Cluster of entities/value objects treated as a unit

**Example:**

```php
// Order Aggregate with Items
$items = [
    OrderItemEntity::create(1, 'Product A', new Money(10), new Quantity(2)),
    OrderItemEntity::create(2, 'Product B', new Money(15), new Quantity(1)),
];

$order = OrderAggregate::create(
    userId: 123,
    items: $items,
    notes: 'Gift wrap please'
);

// Aggregate enforces invariants
$order->calculateTotal(); // Money(35.00)
$order->process(); // Only valid from pending status
```

**Characteristics:**
- One root entity (OrderAggregate)
- Contains child entities (OrderItemEntity)
- Enforces consistency boundaries
- External access only through root
- Transactional boundary

### Domain Events

**Purpose**: Record significant domain occurrences

**Example:**

```php
// Events are automatically recorded
$product = ProductEntity::create(...);
$events = $product->getDomainEvents();
// [ProductCreated(...)]

$product->decrementStock(new Quantity(10));
$events = $product->getDomainEvents();
// [ProductCreated(...), ProductStockDecremented(...)]

// Clear events after processing
$product->clearDomainEvents();
```

**Usage in Application:**

```php
// In Application Service
$user = $userRepository->save($user);

// Dispatch domain events
foreach ($user->getDomainEvents() as $event) {
    event($event); // Laravel event system
}

$user->clearDomainEvents();
```

### Domain Services

**Purpose**: Operations that don't belong to any entity/value object

**When to use:**
- Operation involves multiple aggregates
- Complex validation logic
- External service interaction

**Examples:**

```php
// Password hashing doesn't belong to User entity
$hasher = new UserPasswordHasher();
$hashed = $hasher->hash('plain-password');

// Stock validation crosses product boundaries
$validator = new StockValidator();
$validator->validateMultiple($products, $quantities);
```

### Repositories

**Purpose**: Provide collection-like interface for aggregates

**Interface:**

```php
interface UserRepositoryInterface
{
    public function save(UserEntity $user): UserEntity;
    public function findById(int $id): ?UserEntity;
    public function findByEmail(Email $email): ?UserEntity;
    public function delete(int $id): bool;
    public function getAll(): array;
}
```

**Implementation:**

```php
class EloquentUserDomainRepository implements UserRepositoryInterface
{
    public function save(UserEntity $user): UserEntity
    {
        // Map entity to Eloquent model
        $model = $this->mapToModel($user);
        $model->save();
        
        // Update entity ID
        $user->setId($model->id);
        
        return $user;
    }
    
    public function findById(int $id): ?UserEntity
    {
        $model = User::find($id);
        return $model ? $this->mapToEntity($model) : null;
    }
}
```

---

## Layer Architecture

### 1. Domain Layer (`app/Domains/`)

**Purpose**: Pure business logic, framework-independent

**Contents:**
- Entities
- Value Objects
- Aggregates
- Repository Interfaces
- Domain Services
- Domain Events
- Domain Exceptions

**Rules:**
- No framework dependencies
- No infrastructure concerns
- Pure PHP objects
- Self-contained business logic

### 2. Application Layer (`app/Application/`)

**Purpose**: Orchestrate domain objects for use cases

**Contents:**
- Application Services (Use Cases)
- Command/Query handlers (optional)

**Responsibilities:**
- Transaction management
- Coordinate repositories
- Map DTOs to domain objects
- Dispatch domain events

**Example:**

```php
class CreateUserApplicationService
{
    public function execute(UserData $data): UserEntity
    {
        // 1. Create domain objects
        $email = new Email($data->email);
        $hashedPassword = $this->passwordHasher->hash($data->password);
        
        // 2. Create entity
        $user = UserEntity::create($data->name, $email, $hashedPassword);
        
        // 3. Persist
        $user = $this->userRepository->save($user);
        
        // 4. Dispatch events (optional)
        foreach ($user->getDomainEvents() as $event) {
            event($event);
        }
        
        return $user;
    }
}
```

### 3. Infrastructure Layer (`app/Infrastructure/`)

**Purpose**: Technical implementation details

**Contents:**
- Repository implementations
- External service adapters
- Framework integration

**Example:**

```php
// Maps between domain entities and Eloquent models
class EloquentProductDomainRepository implements ProductRepositoryInterface
{
    private function mapToEntity(Product $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            price: new Money($model->price),
            stock: new Quantity($model->stock),
            status: $model->is_active ? 
                ProductStatus::active() : 
                ProductStatus::inactive()
        );
    }
}
```

### 4. Presentation Layer (`app/Http/`)

**Purpose**: Handle user interface concerns

**Contents:**
- Controllers
- Form Requests
- Resources
- View Models

**Example:**

```php
class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        // 1. Convert request to DTO
        $data = UserData::fromRequest($request);
        
        // 2. Execute use case
        $user = $this->createUserService->execute($data);
        
        // 3. Return response
        return redirect()
            ->route('users.index')
            ->with('success', 'User created');
    }
}
```

---

## Usage Examples

### Creating a User

```php
// In Controller
use App\Application\Services\User\CreateUserApplicationService;
use App\Core\DTOs\UserData;

public function store(StoreUserRequest $request)
{
    $data = UserData::fromRequest($request);
    $user = $this->createUserService->execute($data);
    
    return redirect()->route('users.index');
}

// Application Service handles orchestration
class CreateUserApplicationService
{
    public function execute(UserData $data): UserEntity
    {
        $email = new Email($data->email);
        $hashedPassword = $this->passwordHasher->hash($data->password);
        
        $user = UserEntity::create($data->name, $email, $hashedPassword);
        
        return $this->userRepository->save($user);
    }
}
```

### Creating an Order

```php
// In Controller
use App\Application\Services\Order\CreateOrderApplicationService;
use App\Core\DTOs\OrderData;

public function store(StoreOrderRequest $request)
{
    $data = OrderData::fromRequest($request);
    $order = $this->createOrderService->execute($data);
    
    return redirect()->route('orders.show', $order->getId());
}

// Application Service with transaction
class CreateOrderApplicationService
{
    public function execute(OrderData $data): OrderAggregate
    {
        return DB::transaction(function () use ($data) {
            $orderItems = [];
            
            // Process each item
            foreach ($data->items as $itemData) {
                $product = $this->productRepository->findById($itemData->productId);
                
                // Validate stock
                $quantity = new Quantity($itemData->quantity);
                $this->stockValidator->validate($product, $quantity);
                
                // Decrement stock
                $product->decrementStock($quantity);
                $this->productRepository->save($product);
                
                // Create order item
                $orderItems[] = OrderItemEntity::create(
                    $product->getId(),
                    $product->getName(),
                    $product->getPrice(),
                    $quantity
                );
            }
            
            // Create and save order
            $order = OrderAggregate::create($data->userId, $orderItems, $data->notes);
            return $this->orderRepository->save($order);
        });
    }
}
```

### Updating Order Status

```php
// Application Service
class UpdateOrderStatusApplicationService
{
    public function execute(int $orderId, string $status): OrderAggregate
    {
        $order = $this->orderRepository->findById($orderId);
        
        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }
        
        // Domain enforces valid transitions
        $newStatus = new OrderStatus($status);
        $order->changeStatus($newStatus);
        
        return $this->orderRepository->save($order);
    }
}
```

---

## Best Practices

### 1. Keep Domain Pure

❌ **Bad:**
```php
// Entity depends on framework
class UserEntity
{
    public function save()
    {
        DB::table('users')->insert(...); // Framework dependency!
    }
}
```

✅ **Good:**
```php
// Entity is pure, repository handles persistence
class UserEntity
{
    public function changeName(string $name): void
    {
        $this->name = $name; // Pure domain logic
    }
}

// Repository handles framework interaction
$user->changeName('New Name');
$userRepository->save($user);
```

### 2. Validate in Value Objects

❌ **Bad:**
```php
// Validation in service
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new InvalidArgumentException('Invalid email');
}
```

✅ **Good:**
```php
// Validation in value object constructor
class Email
{
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        $this->value = $email;
    }
}
```

### 3. Enforce Invariants in Aggregates

❌ **Bad:**
```php
// Business rule in service
if (count($items) === 0) {
    throw new EmptyOrderException();
}
```

✅ **Good:**
```php
// Business rule in aggregate
class OrderAggregate
{
    public static function create(int $userId, array $items, ?string $notes = null): self
    {
        if (empty($items)) {
            throw new EmptyOrderException('Order must have at least one item');
        }
        // ...
    }
}
```

### 4. Use Application Services for Coordination

❌ **Bad:**
```php
// Controller does too much
public function store(Request $request)
{
    DB::transaction(function () use ($request) {
        $product = Product::findOrFail($request->product_id);
        $product->decrement('stock', $request->quantity);
        
        $order = Order::create([...]);
        OrderItem::create([...]);
    });
}
```

✅ **Good:**
```php
// Application service orchestrates
public function store(Request $request)
{
    $data = OrderData::fromRequest($request);
    $order = $this->createOrderService->execute($data);
    return redirect()->route('orders.show', $order->getId());
}
```

### 5. Return Domain Objects from Repositories

❌ **Bad:**
```php
// Returns Eloquent model
public function findById(int $id): ?User
{
    return User::find($id);
}
```

✅ **Good:**
```php
// Returns domain entity
public function findById(int $id): ?UserEntity
{
    $model = User::find($id);
    return $model ? $this->mapToEntity($model) : null;
}
```

---

## Migration Guide

### From SOLID to DDD

If you want to migrate existing code to use DDD:

#### Step 1: Identify Aggregates

Determine which entities are aggregate roots:
- **User** → Simple entity (no child entities)
- **Product** → Simple entity
- **Order** → Aggregate root (contains OrderItems)

#### Step 2: Extract Value Objects

Identify concepts that should be value objects:
- Email addresses → `Email` value object
- Money amounts → `Money` value object
- Quantities → `Quantity` value object
- Statuses → `OrderStatus`, `ProductStatus` value objects

#### Step 3: Create Domain Entities

Transform models into rich domain entities:

```php
// Before: Anemic model
class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];
}

// After: Rich domain entity
class ProductEntity
{
    public function decrementStock(Quantity $quantity): void
    {
        if (!$this->hasStock($quantity)) {
            throw new InsufficientStockException();
        }
        $this->stock = $this->stock->subtract($quantity);
        $this->recordEvent(new ProductStockDecremented(...));
    }
}
```

#### Step 4: Create Application Services

Move orchestration logic from domain services to application services:

```php
// Before: Domain service does everything
class OrderService
{
    public function createOrder(OrderData $data): Order
    {
        // Validation, stock management, persistence all mixed
    }
}

// After: Application service orchestrates, domain enforces rules
class CreateOrderApplicationService
{
    public function execute(OrderData $data): OrderAggregate
    {
        // Orchestration only, domain handles rules
    }
}
```

#### Step 5: Update Controllers

Update controllers to use application services:

```php
// Before
public function store(Request $request)
{
    $data = OrderData::fromRequest($request);
    $order = $this->orderService->createOrder($data);
}

// After
public function store(Request $request)
{
    $data = OrderData::fromRequest($request);
    $order = $this->createOrderService->execute($data);
}
```

---

## Conclusion

Domain-Driven Design provides a structured approach to modeling complex business logic. While it adds complexity compared to simple CRUD applications, it pays dividends in:

- **Maintainability**: Business logic is centralized and testable
- **Expressiveness**: Code reads like business requirements
- **Flexibility**: Easy to change implementations without affecting domain
- **Team Communication**: Ubiquitous language improves collaboration

Use DDD when your domain is complex and warrants the investment. For simple CRUD operations, the SOLID architecture may be sufficient.

---

**Questions or Issues?**

Open an issue on GitHub or refer to the main README.md for additional resources.
