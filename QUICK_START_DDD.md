# Quick Start Guide - DDD Architecture

## 🚀 Quick Overview

This Laravel application now includes a **complete Domain-Driven Design (DDD) implementation** alongside the original SOLID architecture.

---

## 📖 Documentation Structure

```
📚 Documentation Files
├── README.md                          # Main documentation (includes DDD overview)
├── DDD_ARCHITECTURE.md               # Complete DDD implementation guide
├── DDD_CHECKLIST.md                  # Implementation verification
├── IMPLEMENTATION_SUMMARY.md         # What was added and statistics
├── QUICK_START_DDD.md               # This file - quick reference
└── docs/DDD-BOUNDED-CONTEXTS.md     # Visual diagrams and bounded contexts
```

**Start Here**: 
- New to DDD? → Read `DDD_ARCHITECTURE.md`
- Want quick examples? → Continue reading this file
- Need visual diagrams? → See `docs/DDD-BOUNDED-CONTEXTS.md`

---

## 🏗️ Architecture at a Glance

### Two Architectures Available

1. **SOLID Architecture** (Original)
   - `app/Core/` - Services, DTOs, Interfaces
   - Simple service-based approach
   - Great for CRUD operations

2. **DDD Architecture** (New)
   - `app/Domains/` - Bounded contexts with rich domain models
   - `app/Application/` - Use case orchestration
   - Great for complex business logic

Both work simultaneously! Choose based on your needs.

---

## 🎯 The Three Domains

### 1️⃣ User Domain
**Path**: `app/Domains/User/`

**Purpose**: User identity and authentication

**Key Classes**:
```php
use App\Domains\User\Entities\UserEntity;
use App\Domains\User\ValueObjects\Email;
use App\Application\Services\User\CreateUserApplicationService;

// Create a user
$service = app(CreateUserApplicationService::class);
$user = $service->execute($userData);
```

### 2️⃣ Product Domain
**Path**: `app/Domains/Product/`

**Purpose**: Product catalog and inventory

**Key Classes**:
```php
use App\Domains\Product\Entities\ProductEntity;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\Quantity;
use App\Application\Services\Product\CreateProductApplicationService;

// Create a product
$service = app(CreateProductApplicationService::class);
$product = $service->execute($productData);
```

### 3️⃣ Order Domain
**Path**: `app/Domains/Order/`

**Purpose**: Order management and fulfillment

**Key Classes**:
```php
use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Order\ValueObjects\OrderStatus;
use App\Application\Services\Order\CreateOrderApplicationService;

// Create an order
$service = app(CreateOrderApplicationService::class);
$order = $service->execute($orderData);
```

---

## 💡 Quick Examples

### Example 1: Working with Value Objects

```php
use App\Domains\User\ValueObjects\Email;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\Quantity;

// Email - Self-validating
$email = new Email('user@example.com'); // ✅ Valid
$email = new Email('invalid-email');     // ❌ Throws exception

// Money - Rich behavior
$price = new Money(19.99, 'USD');
$tax = new Money(2.00, 'USD');
$total = $price->add($tax); // Money(21.99, 'USD')

// Quantity - Non-negative
$stock = new Quantity(100);
$sold = new Quantity(25);
$remaining = $stock->subtract($sold); // Quantity(75)
```

### Example 2: Creating Entities

```php
use App\Domains\Product\Entities\ProductEntity;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\Quantity;
use App\Domains\Product\ValueObjects\ProductStatus;

$product = ProductEntity::create(
    name: 'Laptop',
    description: 'High-performance laptop',
    price: new Money(999.99),
    stock: new Quantity(50),
    status: ProductStatus::active()
);

// Rich behavior
$product->decrementStock(new Quantity(5));
$product->changePrice(new Money(899.99));
```

### Example 3: Working with Aggregates

```php
use App\Domains\Order\Aggregates\OrderAggregate;
use App\Domains\Order\Entities\OrderItemEntity;
use App\Domains\Product\ValueObjects\Money;
use App\Domains\Product\ValueObjects\Quantity;

$items = [
    OrderItemEntity::create(1, 'Laptop', new Money(999), new Quantity(2)),
    OrderItemEntity::create(2, 'Mouse', new Money(25), new Quantity(1)),
];

$order = OrderAggregate::create(
    userId: 123,
    items: $items,
    notes: 'Express delivery'
);

// Aggregate enforces rules
$total = $order->calculateTotal(); // Money(2023.00)
$order->process();                 // Change status
$order->complete();                // Change status again
```

### Example 4: Using Application Services

```php
use App\Application\Services\Order\CreateOrderApplicationService;
use App\Core\DTOs\OrderData;

// In a controller
public function store(StoreOrderRequest $request)
{
    $data = OrderData::fromRequest($request);
    
    $service = app(CreateOrderApplicationService::class);
    $order = $service->execute($data);
    
    return redirect()->route('orders.show', $order->getId());
}

// The application service handles:
// 1. Stock validation
// 2. Stock decrement (with events)
// 3. Order creation (with events)
// 4. Transaction management
// All automatically!
```

### Example 5: Domain Events

```php
use App\Domains\Product\Entities\ProductEntity;

$product = ProductEntity::create(...);

// Events are recorded automatically
$events = $product->getDomainEvents();
// [ProductCreated(...)]

$product->decrementStock(new Quantity(10));
$events = $product->getDomainEvents();
// [ProductCreated(...), ProductStockDecremented(...)]

// Dispatch events (in application service)
foreach ($product->getDomainEvents() as $event) {
    event($event); // Laravel event system
}
$product->clearDomainEvents();
```

---

## 🔧 How to Use

### Option 1: Use DDD Architecture

Inject application services in your controllers:

```php
use App\Application\Services\User\CreateUserApplicationService;

class UserController extends Controller
{
    public function __construct(
        private readonly CreateUserApplicationService $createUserService
    ) {}
    
    public function store(StoreUserRequest $request)
    {
        $data = UserData::fromRequest($request);
        $user = $this->createUserService->execute($data);
        return redirect()->route('users.index');
    }
}
```

### Option 2: Use Original SOLID Architecture

Continue using existing services:

```php
use App\Core\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}
    
    public function store(StoreUserRequest $request)
    {
        $data = UserData::fromRequest($request);
        $user = $this->userService->createUser($data);
        return redirect()->route('users.index');
    }
}
```

Both work! Choose what fits your needs.

---

## 📂 Directory Structure Reference

```
app/
├── Domains/                    # 🆕 DDD Domain Layer
│   ├── User/
│   │   ├── Entities/          # UserEntity
│   │   ├── ValueObjects/      # Email, UserStatus
│   │   ├── Repositories/      # Interfaces
│   │   ├── Services/          # UserPasswordHasher
│   │   ├── Events/            # UserCreated, etc.
│   │   └── Exceptions/        # Domain exceptions
│   ├── Product/
│   │   ├── Entities/          # ProductEntity
│   │   ├── ValueObjects/      # Money, Quantity, etc.
│   │   └── ...
│   └── Order/
│       ├── Aggregates/        # OrderAggregate
│       ├── Entities/          # OrderItemEntity
│       └── ...
│
├── Application/                # 🆕 Use Cases Layer
│   └── Services/
│       ├── User/              # CreateUser, UpdateUser
│       ├── Product/           # CreateProduct, UpdateProduct
│       └── Order/             # CreateOrder, CancelOrder, etc.
│
├── Infrastructure/
│   └── Repositories/
│       └── DDD/               # 🆕 Domain repository implementations
│
├── Core/                      # ✅ Original SOLID
│   ├── DTOs/
│   ├── Interfaces/
│   └── Services/
│
└── Http/                      # ✅ Controllers
    └── Controllers/
```

---

## 🎓 Learning Path

### Beginner
1. Read `README.md` - Overview
2. Review value objects in `app/Domains/*/ValueObjects/`
3. Look at entities in `app/Domains/*/Entities/`

### Intermediate
1. Study `DDD_ARCHITECTURE.md` - Deep dive
2. Examine application services in `app/Application/Services/`
3. Review repository implementations in `app/Infrastructure/Repositories/DDD/`

### Advanced
1. Read `docs/DDD-BOUNDED-CONTEXTS.md` - Architecture patterns
2. Study aggregate in `app/Domains/Order/Aggregates/`
3. Implement your own domain using the patterns

---

## 🧪 Testing Tip

### Test Value Objects
```php
// They're pure, easy to test!
$email1 = new Email('test@example.com');
$email2 = new Email('test@example.com');
$this->assertTrue($email1->equals($email2));
```

### Test Entities
```php
// Test business logic
$product = ProductEntity::create(...);
$this->expectException(InsufficientStockException::class);
$product->decrementStock(new Quantity(1000));
```

### Test Application Services
```php
// Test use cases
$service = new CreateOrderApplicationService($orderRepo, $productRepo, $validator);
$order = $service->execute($orderData);
$this->assertInstanceOf(OrderAggregate::class, $order);
```

---

## ❓ Common Questions

### Q: Should I use DDD or SOLID?
**A**: Use SOLID for simple CRUD. Use DDD for complex business logic.

### Q: Can I mix both approaches?
**A**: Yes! They coexist. Use DDD where complexity warrants it.

### Q: Are the existing controllers broken?
**A**: No! All existing code works unchanged.

### Q: Where do I start learning DDD?
**A**: Start with value objects, they're the easiest pattern.

### Q: Do I need to migrate everything to DDD?
**A**: No! Migrate only where you need rich domain logic.

---

## 🔗 Key Files to Study

| Pattern | Example File | Complexity |
|---------|--------------|------------|
| Value Object | `app/Domains/Product/ValueObjects/Money.php` | ⭐ Easy |
| Entity | `app/Domains/Product/Entities/ProductEntity.php` | ⭐⭐ Medium |
| Aggregate | `app/Domains/Order/Aggregates/OrderAggregate.php` | ⭐⭐⭐ Complex |
| Application Service | `app/Application/Services/Order/CreateOrderApplicationService.php` | ⭐⭐ Medium |
| Repository | `app/Infrastructure/Repositories/DDD/EloquentProductDomainRepository.php` | ⭐⭐ Medium |

---

## 📞 Get Help

- **Full Guide**: See `DDD_ARCHITECTURE.md`
- **Visual Diagrams**: See `docs/DDD-BOUNDED-CONTEXTS.md`
- **Implementation Details**: See `IMPLEMENTATION_SUMMARY.md`

---

**Happy Coding! 🚀**

*Remember: DDD is a tool, not a requirement. Use it where it adds value.*
