# Laravel SOLID Principles Example

A comprehensive Laravel application demonstrating clean architecture and **SOLID principles** implementation through a User Management system.

---

## 📁 Project Structure

```
app/
├── Core/                          # Core Business Logic (Domain Layer)
│   ├── DTOs/                      # Data Transfer Objects
│   │   ├── UserData.php
│   │   └── UserDetailData.php
│   ├── Interfaces/                # Contracts/Abstractions
│   │   └── UserRepositoryInterface.php
│   └── Services/                  # Business Logic Services
│       └── UserService.php
│
├── Http/                          # HTTP Layer (Presentation)
│   ├── Controllers/
│   │   └── Web/
│   │       └── UserController.php
│   └── Requests/
│       └── User/
│           ├── StoreUserRequest.php
│           └── UpdateUserRequest.php
│
├── Infrastructure/                # Infrastructure Layer
│   └── Repositories/
│       └── EloquentUserRepository.php
│
├── Models/                        # Eloquent Models
│   ├── User.php
│   └── UserDetail.php
│
└── Providers/
    └── AppServiceProvider.php     # Dependency Injection Bindings
```

---

## 🎯 SOLID Principles Demonstrated

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

**Made with ❤️ to demonstrate clean code architecture**
