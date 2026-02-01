# DDD Bounded Contexts Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         Laravel Application                              │
│                     (Shopping Management System)                         │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    │               │               │
                    ▼               ▼               ▼
        ┌───────────────┐   ┌──────────────┐   ┌──────────────┐
        │     USER      │   │   PRODUCT    │   │    ORDER     │
        │   CONTEXT     │   │   CONTEXT    │   │   CONTEXT    │
        └───────────────┘   └──────────────┘   └──────────────┘
```

---

## 1. User Bounded Context

**Responsibility**: User identity, authentication, and profile management

### Core Components

```
User Context
├── Entities
│   └── UserEntity
│       ├── id: int
│       ├── name: string
│       ├── email: Email (VO)
│       ├── hashedPassword: string
│       └── Methods:
│           ├── create()
│           ├── changeName()
│           ├── changeEmail()
│           ├── changePassword()
│           └── verifyEmail()
│
├── Value Objects
│   ├── Email
│   │   └── Validates and normalizes email
│   └── UserStatus
│       └── active | inactive | suspended
│
├── Domain Services
│   └── UserPasswordHasher
│       ├── hash()
│       └── verify()
│
├── Events
│   ├── UserCreated
│   ├── UserEmailChanged
│   └── UserPasswordChanged
│
└── Repositories
    └── UserRepositoryInterface
        ├── save()
        ├── findById()
        ├── findByEmail()
        └── delete()
```

### Business Rules (Invariants)
- ✅ Email must be valid format
- ✅ Email must be unique
- ✅ Password must be hashed
- ✅ User status must be valid

---

## 2. Product Bounded Context

**Responsibility**: Product catalog, inventory, and pricing management

### Core Components

```
Product Context
├── Entities
│   └── ProductEntity
│       ├── id: int
│       ├── name: string
│       ├── description: string
│       ├── price: Money (VO)
│       ├── stock: Quantity (VO)
│       ├── status: ProductStatus (VO)
│       └── Methods:
│           ├── create()
│           ├── changeName()
│           ├── changePrice()
│           ├── decrementStock()
│           ├── incrementStock()
│           ├── activate()
│           └── deactivate()
│
├── Value Objects
│   ├── Money
│   │   ├── amount: float
│   │   ├── currency: string
│   │   └── Operations: add(), subtract(), multiply()
│   │
│   ├── Quantity
│   │   ├── value: int
│   │   └── Operations: add(), subtract()
│   │
│   ├── ProductSKU
│   │   └── Unique product identifier
│   │
│   └── ProductStatus
│       └── active | inactive
│
├── Domain Services
│   └── StockValidator
│       └── validate()
│
├── Events
│   ├── ProductCreated
│   ├── ProductStockDecremented
│   ├── ProductStockIncremented
│   └── ProductPriceChanged
│
└── Repositories
    └── ProductRepositoryInterface
        ├── save()
        ├── findById()
        ├── delete()
        ├── getAll()
        └── getAllActive()
```

### Business Rules (Invariants)
- ✅ Stock cannot be negative
- ✅ Price cannot be negative
- ✅ Stock decrement only if sufficient quantity
- ✅ SKU must be unique (if used)

---

## 3. Order Bounded Context

**Responsibility**: Order lifecycle, fulfillment, and status management

### Core Components

```
Order Context
├── Aggregates
│   └── OrderAggregate (Root)
│       ├── id: int
│       ├── userId: int
│       ├── status: OrderStatus (VO)
│       ├── items: OrderItemEntity[]
│       ├── notes: string
│       └── Methods:
│           ├── create()
│           ├── changeStatus()
│           ├── cancel()
│           ├── process()
│           ├── complete()
│           ├── addItem()
│           └── calculateTotal()
│
├── Entities
│   └── OrderItemEntity
│       ├── id: int
│       ├── productId: int
│       ├── productName: string
│       ├── unitPrice: Money (VO)
│       ├── quantity: Quantity (VO)
│       └── Methods:
│           ├── create()
│           └── getSubtotal()
│
├── Value Objects
│   └── OrderStatus
│       ├── pending
│       ├── processing
│       ├── completed
│       └── cancelled
│       └── Methods: canTransitionTo()
│
├── Domain Services
│   └── OrderTotalCalculator
│       └── calculate()
│
├── Events
│   ├── OrderCreated
│   ├── OrderStatusChanged
│   └── OrderCancelled
│
└── Repositories
    └── OrderRepositoryInterface
        ├── save()
        ├── findById()
        ├── findByUserId()
        ├── delete()
        └── getAll()
```

### Business Rules (Invariants)
- ✅ Order must have at least one item
- ✅ Status transitions must be valid:
  - pending → processing | cancelled
  - processing → completed | cancelled
  - completed → (no transitions)
  - cancelled → (no transitions)
- ✅ Total equals sum of item subtotals
- ✅ Items cannot be modified after order is completed

---

## Context Relationships

```
┌─────────────────┐
│  User Context   │
│                 │
│  UserEntity     │──────┐
└─────────────────┘      │
                         │ userId
                         │
                         ▼
                    ┌─────────────────┐
                    │ Order Context   │
                    │                 │
┌─────────────────┐ │ OrderAggregate  │
│ Product Context │ │  ├─ userId      │
│                 │ │  └─ items[]     │
│ ProductEntity   │◀┼────────┘        │
└─────────────────┘ └─────────────────┘
      productId

Relationships:
- Order → User (by userId)
- OrderItem → Product (by productId)
- Contexts are loosely coupled (only by ID references)
```

### Anti-Corruption Layer

The Order context doesn't directly depend on User or Product entities.
Instead, it references them by ID and stores denormalized data (productName, unitPrice).

**Benefits:**
- Contexts can evolve independently
- No tight coupling between domains
- Each context can have its own database if needed
- Clear boundaries and responsibilities

---

## Application Services Layer

Application Services orchestrate domain objects to fulfill use cases.

```
Application Layer
├── User/
│   ├── CreateUserApplicationService
│   │   └── execute(UserData): UserEntity
│   │
│   └── UpdateUserApplicationService
│       └── execute(int, UserData): UserEntity
│
├── Product/
│   ├── CreateProductApplicationService
│   │   └── execute(ProductData): ProductEntity
│   │
│   └── UpdateProductApplicationService
│       └── execute(int, ProductData): ProductEntity
│
└── Order/
    ├── CreateOrderApplicationService
    │   └── execute(OrderData): OrderAggregate
    │       └── [Transaction boundary]
    │           ├── Validate stock
    │           ├── Decrement product stock
    │           ├── Create order aggregate
    │           └── Persist order
    │
    ├── UpdateOrderStatusApplicationService
    │   └── execute(int, string): OrderAggregate
    │
    └── CancelOrderApplicationService
        └── execute(int): OrderAggregate
            └── [Transaction boundary]
                ├── Load order
                ├── Restore product stock
                ├── Cancel order
                └── Persist changes
```

---

## Data Flow Example: Create Order

```
1. HTTP Request
   ↓
2. OrderController::store(StoreOrderRequest)
   ↓
3. Convert to DTO: OrderData::fromRequest()
   ↓
4. CreateOrderApplicationService::execute(OrderData)
   ├─→ Start DB Transaction
   │
   ├─→ For each item:
   │   ├─ Load ProductEntity from repository
   │   ├─ Validate stock (StockValidator)
   │   ├─ ProductEntity::decrementStock()
   │   ├─ Save ProductEntity (emits ProductStockDecremented)
   │   └─ Create OrderItemEntity
   │
   ├─→ OrderAggregate::create(userId, items, notes)
   │   └─ (emits OrderCreated event)
   │
   ├─→ Save OrderAggregate to repository
   │
   └─→ Commit Transaction
   ↓
5. Return OrderAggregate to Controller
   ↓
6. Redirect to order details page
```

---

## Key DDD Principles Applied

### 1. Ubiquitous Language
Domain terms match business vocabulary:
- "Order" (not "Request" or "Purchase")
- "Stock" (not "Inventory Count")
- "Decrement" (not "Reduce" or "Subtract")
- "Status" (not "State" or "Phase")

### 2. Bounded Contexts
Clear separation of concerns:
- User Context: Identity & Access
- Product Context: Catalog & Inventory
- Order Context: Sales & Fulfillment

### 3. Aggregates
- OrderAggregate ensures consistency of order and its items
- External objects cannot directly modify OrderItems
- All modifications go through OrderAggregate root

### 4. Value Objects
- Self-validating (Email, Money, Quantity)
- Immutable (readonly classes)
- Rich behavior (operations like add, subtract)

### 5. Domain Events
- Capture important occurrences
- Enable loose coupling
- Support event-driven architecture
- Can trigger side effects (emails, notifications)

### 6. Invariants
Business rules enforced at compile-time:
- Stock cannot go negative (Quantity validates)
- Invalid status transitions blocked (OrderStatus::canTransitionTo)
- Empty orders prevented (OrderAggregate::create validates)

---

## Benefits of This Architecture

✅ **Maintainability**: Clear boundaries, easy to understand
✅ **Testability**: Domain logic isolated from framework
✅ **Scalability**: Contexts can be deployed separately
✅ **Flexibility**: Easy to swap implementations
✅ **Expressiveness**: Code reads like business requirements
✅ **Collaboration**: Common language with domain experts

---

## When to Use Each Context

| Use Case | Context | Entry Point |
|----------|---------|-------------|
| Register new user | User | `CreateUserApplicationService` |
| Update user profile | User | `UpdateUserApplicationService` |
| Add product to catalog | Product | `CreateProductApplicationService` |
| Adjust product price | Product | `UpdateProductApplicationService` |
| Place order | Order | `CreateOrderApplicationService` |
| Update order status | Order | `UpdateOrderStatusApplicationService` |
| Cancel order | Order | `CancelOrderApplicationService` |

---

For detailed implementation examples, see `DDD_ARCHITECTURE.md`.
