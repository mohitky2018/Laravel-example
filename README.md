# Laravel SOLID Principles & Domain-Driven Design (DDD) Example

A comprehensive Laravel application demonstrating clean architecture, **SOLID principles**, and **Domain-Driven Design (DDD)** patterns through User, Product, and Order Management systems.

---

## 🏗️ Architecture Overview

This project now implements **TWO architectural approaches** side-by-side:

1. **SOLID Architecture** (Legacy) - Simple service-based architecture
2. **Domain-Driven Design (DDD)** - Advanced domain modeling with tactical patterns

Both architectures coexist, allowing you to learn and compare different approaches to building maintainable applications.

---

## 📁 Project Structure

### SOLID Architecture (Legacy)
```
app/
├── Core/                          # Core Business Logic (Domain Layer)
│   ├── DTOs/                      # Data Transfer Objects
│   │   ├── UserData.php
│   │   ├── UserDetailData.php
│   │   ├── ProductData.php
│   │   ├── OrderData.php
│   │   └── OrderItemData.php
│   ├── Interfaces/                # Contracts/Abstractions
│   │   ├── UserRepositoryInterface.php
│   │   ├── ProductRepositoryInterface.php
│   │   └── OrderRepositoryInterface.php
│   └── Services/                  # Business Logic Services
│       ├── UserService.php
│       ├── ProductService.php
│       └── OrderService.php
│
├── Http/                          # HTTP Layer (Presentation)
│   ├── Controllers/
│   │   └── Web/
│   │       ├── UserController.php
│   │       ├── ProductController.php
│   │       └── OrderController.php
│   └── Requests/
│       ├── User/
│       ├── Product/
│       └── Order/
│
├── Infrastructure/                # Infrastructure Layer
│   └── Repositories/
│       ├── EloquentUserRepository.php
│       ├── EloquentProductRepository.php
│       └── EloquentOrderRepository.php
│
├── Models/                        # Eloquent Models
│   ├── User.php
│   ├── UserDetail.php
│   ├── Product.php
│   ├── Order.php
│   └── OrderItem.php
│
└── Providers/
    └── AppServiceProvider.php     # Dependency Injection Bindings
```

### Domain-Driven Design (DDD) Architecture
```
app/
├── Domains/                       # Domain Layer (Business Core)
│   ├── User/                      # User Bounded Context
│   │   ├── Entities/              # Domain Entities
│   │   │   └── UserEntity.php
│   │   ├── ValueObjects/          # Immutable Value Objects
│   │   │   ├── Email.php
│   │   │   └── UserStatus.php
│   │   ├── Repositories/          # Repository Interfaces
│   │   │   └── UserRepositoryInterface.php
│   │   ├── Services/              # Domain Services
│   │   │   └── UserPasswordHasher.php
│   │   ├── Events/                # Domain Events
│   │   │   ├── UserCreated.php
│   │   │   ├── UserEmailChanged.php
│   │   │   └── UserPasswordChanged.php
│   │   └── Exceptions/            # Domain Exceptions
│   │       └── UserNotFoundException.php
│   │
│   ├── Product/                   # Product Bounded Context
│   │   ├── Entities/
│   │   │   └── ProductEntity.php
│   │   ├── ValueObjects/
│   │   │   ├── Money.php
│   │   │   ├── ProductSKU.php
│   │   │   ├── Quantity.php
│   │   │   └── ProductStatus.php
│   │   ├── Repositories/
│   │   │   └── ProductRepositoryInterface.php
│   │   ├── Services/
│   │   │   └── StockValidator.php
│   │   ├── Events/
│   │   │   ├── ProductCreated.php
│   │   │   ├── ProductStockDecremented.php
│   │   │   ├── ProductStockIncremented.php
│   │   │   └── ProductPriceChanged.php
│   │   └── Exceptions/
│   │       ├── InsufficientStockException.php
│   │       └── ProductNotFoundException.php
│   │
│   └── Order/                     # Order Bounded Context
│       ├── Aggregates/            # Aggregate Roots
│       │   └── OrderAggregate.php
│       ├── Entities/
│       │   └── OrderItemEntity.php
│       ├── ValueObjects/
│       │   └── OrderStatus.php
│       ├── Repositories/
│       │   └── OrderRepositoryInterface.php
│       ├── Services/
│       │   └── OrderTotalCalculator.php
│       ├── Events/
│       │   ├── OrderCreated.php
│       │   ├── OrderStatusChanged.php
│       │   └── OrderCancelled.php
│       └── Exceptions/
│           ├── InvalidOrderTransitionException.php
│           ├── EmptyOrderException.php
│           └── OrderNotFoundException.php
│
├── Application/                   # Application Layer (Use Cases)
│   └── Services/
│       ├── User/
│       │   ├── CreateUserApplicationService.php
│       │   └── UpdateUserApplicationService.php
│       ├── Product/
│       │   ├── CreateProductApplicationService.php
│       │   └── UpdateProductApplicationService.php
│       └── Order/
│           ├── CreateOrderApplicationService.php
│           ├── UpdateOrderStatusApplicationService.php
│           └── CancelOrderApplicationService.php
│
├── Infrastructure/                # Infrastructure Layer
│   └── Repositories/
│       ├── DDD/                   # DDD Repository Implementations
│       │   ├── EloquentUserDomainRepository.php
│       │   ├── EloquentProductDomainRepository.php
│       │   └── EloquentOrderDomainRepository.php
│       ├── EloquentUserRepository.php
│       ├── EloquentProductRepository.php
│       └── EloquentOrderRepository.php
│
├── Models/                        # Eloquent Models (Infrastructure)
└── Providers/
    └── AppServiceProvider.php     # Dependency Injection Bindings
```

---

## 🛒 Features

### Users Module
- Full CRUD operations
- User details management
- Password hashing via Service layer

### Products Module
- Full CRUD operations
- Stock management
- Active/Inactive status
- Price and inventory tracking

### Orders Module (Mall-style)
- Create orders with multiple products
- Automatic stock deduction
- Order status management (pending → processing → completed/cancelled)
- Total amount calculation
- User assignment

---


### 1. **S** — Single Responsibility Principle (SRP)

> *"A class should have one, and only one, reason to change."*

| Layer | Class | Responsibility |
|-------|-------|----------------|
| **Controller** | `UserController` | Handle HTTP requests and responses |
| **Service** | `UserService` | Execute business logic (password hashing, validation) |
| **Repository** | `EloquentUserRepository` | Database operations only |
| **DTO** | `UserData` | Transport data between layers |

**Example:**
```php
// UserController only handles HTTP concerns
public function store(StoreUserRequest $request): RedirectResponse
{
    $userData = UserData::fromRequest($request);
    $this->userService->createUser($userData);
    return redirect()->route('users.index')->with('success', 'User created.');
}
```

---

### 2. **O** — Open/Closed Principle (OCP)

> *"Software entities should be open for extension, but closed for modification."*

Add new repository implementations **without modifying** existing code:

```php
// Add a new cache repository without changing UserService
class CachedUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EloquentUserRepository $repository,
        private CacheManager $cache
    ) {}
    
    public function findById(int $id): ?User
    {
        return $this->cache->remember("user.{$id}", 3600, fn() => 
            $this->repository->findById($id)
        );
    }
}
```

---

### 3. **L** — Liskov Substitution Principle (LSP)

> *"Objects should be replaceable with their subtypes without affecting correctness."*

Any implementation of `UserRepositoryInterface` can replace another:

```php
// Both implementations are interchangeable
class EloquentUserRepository implements UserRepositoryInterface { }
class MongoUserRepository implements UserRepositoryInterface { }
class InMemoryUserRepository implements UserRepositoryInterface { }  // For testing
```

---

### 4. **I** — Interface Segregation Principle (ISP)

> *"Clients should not be forced to depend on interfaces they don't use."*

The `UserRepositoryInterface` defines **only** the methods needed for user operations:

```php
interface UserRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?User;
    public function create(UserData $data): User;
    public function update(int $id, UserData $data): User;
    public function delete(int $id): bool;
}
```

---

### 5. **D** — Dependency Inversion Principle (DIP)

> *"High-level modules should not depend on low-level modules. Both should depend on abstractions."*

```php
// ❌ Bad: Service depends on concrete implementation
class UserService {
    public function __construct(EloquentUserRepository $repo) {}
}

// ✅ Good: Service depends on abstraction (interface)
class UserService {
    public function __construct(UserRepositoryInterface $repo) {}
}
```

**Binding in AppServiceProvider:**
```php
$this->app->bind(
    UserRepositoryInterface::class,
    EloquentUserRepository::class
);
```

---

## 💡 Why Follow This Structure?

### **1. Testability**

Easily mock dependencies for unit testing:

```php
class UserServiceTest extends TestCase
{
    public function test_creates_user_with_hashed_password()
    {
        $mockRepo = Mockery::mock(UserRepositoryInterface::class);
        $mockRepo->shouldReceive('create')->once();
        
        $service = new UserService($mockRepo);
        $service->createUser(new UserData(...));
    }
}
```

### **2. Flexibility & Maintainability**

| Change Needed | Files Modified |
|---------------|----------------|
| Switch to MongoDB | Only `MongoUserRepository` + binding |
| Add caching layer | Add decorator, change binding |
| Change password algorithm | Only `UserService` |
| Modify API response | Only `UserController` |

### **3. Scalability**

Add new features without touching existing code:
- New repositories (Redis, API, file-based)
- New services (NotificationService, AuditService)
- New controllers (API, Console)

### **4. Clear Separation of Concerns**

```
HTTP Request
    ↓
Controller (validates, converts to DTO)
    ↓
Service (business logic, rules)
    ↓
Repository (database operations)
    ↓
Model (data structure)
```

### **5. Framework Independence**

The `Core/` layer has **zero Laravel dependencies**. Business logic can be:
- Migrated to another framework
- Used in console commands
- Called from queue workers
- Tested without Laravel's HTTP layer

---

## 🔄 Data Flow

```
┌─────────────┐     ┌─────────┐     ┌─────────────┐     ┌────────────┐
│   Request   │────▶│   DTO   │────▶│   Service   │────▶│ Repository │
│  (HTTP)     │     │(UserData)│     │(UserService)│     │(Eloquent)  │
└─────────────┘     └─────────┘     └─────────────┘     └────────────┘
                                           │                    │
                                           ▼                    ▼
                                   Business Logic          Database
                                   (hash password)         (persist)
```

---

## 📊 Traditional vs SOLID Comparison

| Aspect | Traditional (Fat Controller) | SOLID Architecture |
|--------|------------------------------|-------------------|
| **Testing** | Difficult, requires DB | Easy with mocks |
| **Code Reuse** | Low | High |
| **Change Impact** | Ripples everywhere | Isolated |
| **Team Scaling** | Merge conflicts | Parallel work |
| **Debugging** | Hunt through layers | Clear boundaries |
| **New Features** | Modify existing code | Add new classes |

---

## 🚀 Getting Started

```bash
# Clone and install
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Run application
php artisan serve
```

---

## 📚 Key Files Reference

| File | Purpose | SOLID Principle |
|------|---------|-----------------|
| `UserController.php` | HTTP handling | SRP |
| `UserService.php` | Business logic | SRP, DIP |
| `UserRepositoryInterface.php` | Abstraction | DIP, ISP |
| `EloquentUserRepository.php` | Data persistence | OCP, LSP |
| `UserData.php` | Data transfer | SRP |
| `AppServiceProvider.php` | Dependency binding | DIP |

---

## ✅ Best Practices Followed

- **Strict Types**: `declare(strict_types=1);`
- **Readonly Classes**: Immutable DTOs
- **Constructor Injection**: All dependencies injected
- **PHPDoc Comments**: Full documentation
- **Database Transactions**: Atomic operations
- **Type Declarations**: Full return types

---

## 📖 Learn More

- [SOLID Principles Explained](https://en.wikipedia.org/wiki/SOLID)
- [Laravel Service Container](https://laravel.com/docs/container)
- [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)

---

## 🎯 Domain-Driven Design (DDD) Patterns

### What is DDD?

Domain-Driven Design is an approach to software development that emphasizes:
- **Ubiquitous Language**: A common language between developers and domain experts
- **Bounded Contexts**: Clear boundaries between different parts of the system
- **Domain Models**: Rich business logic encapsulated in domain objects
- **Tactical Patterns**: Building blocks for implementing domain models

---

### DDD Building Blocks Implemented

#### 1. **Value Objects**

Immutable objects that are defined by their values, not identity.

**Examples:**
```php
// Email Value Object - Validates and normalizes email addresses
$email = new Email('user@example.com');
echo $email->getValue(); // user@example.com

// Money Value Object - Handles currency operations safely
$price = new Money(19.99, 'USD');
$total = $price->multiply(3); // Money(59.97, 'USD')

// Quantity Value Object - Ensures non-negative quantities
$stock = new Quantity(100);
$remaining = $stock->subtract(new Quantity(30)); // Quantity(70)
```

**Characteristics:**
- ✅ Immutable (readonly classes)
- ✅ Value equality (compare by value, not reference)
- ✅ Self-validating (invalid states impossible)
- ✅ Rich behavior (operations like add, subtract, multiply)

---

#### 2. **Entities**

Objects with unique identity that persists over time.

**Example:**
```php
// User Entity - Has identity (ID) and lifecycle
$user = UserEntity::create(
    name: 'John Doe',
    email: new Email('john@example.com'),
    hashedPassword: $hashedPassword
);

$user->changeName('Jane Doe'); // Maintains same identity
$user->changeEmail(new Email('jane@example.com'));
```

**Characteristics:**
- ✅ Unique identity (ID)
- ✅ Mutable state
- ✅ Identity equality (compare by ID)
- ✅ Encapsulates business rules
- ✅ Records domain events

---

#### 3. **Aggregates**

A cluster of domain objects treated as a single unit, with one root entity.

**Example:**
```php
// Order Aggregate - Root that manages OrderItems
$order = OrderAggregate::create(
    userId: 1,
    items: [$item1, $item2, $item3],
    notes: 'Express delivery'
);

// Aggregate ensures invariants
$order->process();     // Validates state transition
$order->complete();    // Another valid transition
$total = $order->calculateTotal(); // Enforces business rules
```

**Characteristics:**
- ✅ Consistency boundary
- ✅ Enforces invariants
- ✅ External objects access only through root
- ✅ Transactional boundary

---

#### 4. **Domain Events**

Records that something important happened in the domain.

**Examples:**
```php
// Events are recorded when domain operations occur
UserCreated
UserEmailChanged
UserPasswordChanged
ProductCreated
ProductStockDecremented
ProductPriceChanged
OrderCreated
OrderStatusChanged
OrderCancelled
```

**Usage:**
```php
$user = UserEntity::create(...);
$events = $user->getDomainEvents();
// [UserCreated(...)]

// Events can be dispatched to listeners for side effects
foreach ($events as $event) {
    event($event); // Send email, update cache, etc.
}
```

---

#### 5. **Domain Services**

Operations that don't naturally belong to an entity or value object.

**Examples:**
```php
// UserPasswordHasher - Cross-cutting password hashing logic
$hasher = new UserPasswordHasher();
$hashed = $hasher->hash('password123');

// StockValidator - Complex validation logic
$validator = new StockValidator();
$validator->validate($product, new Quantity(5));

// OrderTotalCalculator - Calculation service
$calculator = new OrderTotalCalculator();
$total = $calculator->calculate($order);
```

---

#### 6. **Repository Pattern**

Provides collection-like interface for accessing aggregates.

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

**Characteristics:**
- ✅ Work with aggregate roots only
- ✅ Hide persistence implementation
- ✅ Collection-like interface
- ✅ Domain-centric (returns entities, not models)

---

#### 7. **Application Services (Use Cases)**

Orchestrate domain objects to fulfill application use cases.

**Example:**
```php
class CreateOrderApplicationService
{
    public function execute(OrderData $data): OrderAggregate
    {
        return DB::transaction(function () use ($data) {
            // 1. Validate stock availability
            $this->stockValidator->validate($product, $quantity);
            
            // 2. Decrement stock
            $product->decrementStock($quantity);
            $this->productRepository->save($product);
            
            // 3. Create order aggregate
            $order = OrderAggregate::create(...);
            
            // 4. Persist order
            return $this->orderRepository->save($order);
        });
    }
}
```

**Characteristics:**
- ✅ Transaction boundary
- ✅ Orchestrates domain objects
- ✅ Thin layer (no business logic)
- ✅ Maps DTOs to domain objects

---

### Bounded Contexts

This application implements **three bounded contexts**:

#### 1. **User Context**
- Manages user identity and authentication
- Value Objects: `Email`, `UserStatus`
- Entity: `UserEntity`
- Events: `UserCreated`, `UserEmailChanged`, `UserPasswordChanged`

#### 2. **Product Context**
- Manages product catalog and inventory
- Value Objects: `Money`, `Quantity`, `ProductSKU`, `ProductStatus`
- Entity: `ProductEntity`
- Events: `ProductCreated`, `ProductStockDecremented`, `ProductStockIncremented`

#### 3. **Order Context**
- Manages order lifecycle and fulfillment
- Value Objects: `OrderStatus`, reuses `Money`, `Quantity`
- Aggregate: `OrderAggregate` (root), `OrderItemEntity`
- Events: `OrderCreated`, `OrderStatusChanged`, `OrderCancelled`

---

### DDD vs SOLID Comparison

| Aspect | SOLID Architecture | DDD Architecture |
|--------|-------------------|------------------|
| **Complexity** | Simpler, easier to start | More complex, better for large domains |
| **Domain Logic** | In Service classes | In Entities, Value Objects, Aggregates |
| **Validation** | Form Requests + Services | Self-validating Value Objects |
| **Invariants** | Service methods | Aggregate roots |
| **Events** | Laravel Events | Domain Events |
| **Repository Returns** | Eloquent Models | Domain Entities |
| **Best For** | CRUD apps, smaller systems | Complex business logic, large systems |

---

### Key DDD Concepts

#### Ubiquitous Language

Domain terms used consistently in code and conversations:

| Term | Definition | Code |
|------|------------|------|
| **Order** | Customer purchase with items | `OrderAggregate` |
| **Stock** | Available product quantity | `Quantity` value object |
| **Price** | Product cost | `Money` value object |
| **Status** | Current order state | `OrderStatus` value object |
| **Decrement** | Reduce stock | `ProductEntity::decrementStock()` |

#### Invariants

Business rules that must always be true:

```php
// ✅ Order must have at least one item
OrderAggregate::create() // throws EmptyOrderException

// ✅ Stock cannot go negative
$product->decrementStock($quantity) // throws InsufficientStockException

// ✅ Order status transitions are controlled
$order->cancel() // Only allowed from pending/processing
```

---

### DDD Data Flow

```
┌────────────┐     ┌─────────────────┐     ┌──────────────────┐
│   HTTP     │────▶│  Application    │────▶│  Domain          │
│ Controller │     │   Service       │     │  (Aggregate)     │
└────────────┘     │  (Use Case)     │     │  + Events        │
                   └─────────────────┘     └──────────────────┘
                            │                        │
                            ▼                        ▼
                   ┌─────────────────┐     ┌──────────────────┐
                   │   Repository    │────▶│  Infrastructure  │
                   │   (Interface)   │     │  (Eloquent)      │
                   └─────────────────┘     └──────────────────┘
```

**Flow:**
1. **Controller** receives HTTP request
2. **Application Service** orchestrates use case
3. **Domain Objects** enforce business rules
4. **Repository** persists aggregates
5. **Domain Events** trigger side effects

---

### When to Use DDD?

✅ **Use DDD When:**
- Complex business logic
- Large team collaboration
- Domain experts available
- Long-term project (years)
- Multiple bounded contexts
- Frequent requirement changes

❌ **Skip DDD When:**
- Simple CRUD operations
- Small project scope
- Solo developer
- Tight deadlines
- Static requirements
- No domain complexity

---

### Learning Resources

#### DDD Concepts
- [Domain-Driven Design by Eric Evans](https://www.domainlanguage.com/ddd/)
- [Implementing Domain-Driven Design by Vaughn Vernon](https://vaughnvernon.com/)
- [DDD Reference by Eric Evans](https://www.domainlanguage.com/ddd/reference/)

#### Patterns
- [Martin Fowler - Domain Model](https://martinfowler.com/eaaCatalog/domainModel.html)
- [Martin Fowler - Value Object](https://martinfowler.com/bliki/ValueObject.html)
- [Aggregate Pattern](https://martinfowler.com/bliki/DDD_Aggregate.html)

---

**Made with ❤️ to demonstrate clean code architecture, SOLID principles, and Domain-Driven Design**
