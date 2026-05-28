# E-commerce Lite

**Modern e-commerce platform on Symfony 7.2 with hexagonal architecture**

Lightweight e-commerce platform with a clean Domain layer, Use Case-oriented Application layer, and replaceable infrastructure adapters.

---

## Features

- **Hexagonal Architecture** (Ports & Adapters) — business logic independent of frameworks
- **Domain-Driven Design** — 8 clean entities, 6 port interfaces
- **Use Case orientation** — 8 Application services for key scenarios
- **PHP 8 Attributes** — Doctrine configuration via PHP 8 attributes (no XML mapping)
- **TDD approach** — tests for Application and Domain layers
- **Static analysis** — PHPStan Level 6, Rector
- **Docker First** — PostgreSQL 16 via Docker Compose
- **DI Container** — Symfony Dependency Injection, no singletons
- **Admin Panel** — EasyAdmin 4 with full CRUD for Products, Categories, Orders

---

## Architecture

The project follows **hexagonal architecture** (Ports & Adapters), where business logic is isolated from external dependencies.

```
+-----------------------------------------------------------+
|              HTTP Request (REST API / Admin)               |
|              CLI Commands / Tests                          |
+-----------------------------------------------------------+
                        | Uses
+-----------------------------------------------------------+
|         Controllers (Primary Adapters)                     |
|  CartController, OrderController, WebhookController       |
|  DashboardController, ProductCrudController, etc.         |
|  - Accept request                                         |
|  - Validate input                                         |
|  - Call Use Case                                          |
|  - Return response                                        |
+-----------------------------------------------------------+
                        | Uses
+-----------------------------------------------------------+
|           Application (Use Cases)                          |
|  AddItemToCart, CreateOrder, CheckoutCart, etc.           |
|  - Pure business logic                                    |
|  - Depend on Domain Interfaces                            |
|  - No infrastructure dependencies                         |
+-----------------------------------------------------------+
                        | Uses
+-----------------------------------------------------------+
|          Domain (Ports + Entities + Exceptions)            |
|  Repository Interfaces, Gateway Interface                 |
|  Domain Entities, Domain Exceptions                       |
|  - Enterprise business rules                              |
|  - No external dependencies                               |
|  - Pure PHP                                               |
+-----------------------------------------------------------+
                        | Implemented by
+-----------------------------------------------------------+
|       Infrastructure (Secondary Adapters)                  |
|  Doctrine Repositories, MockPaymentGateway                |
|  - Implement interfaces                                   |
|  - Database operations                                    |
|  - External services                                      |
+-----------------------------------------------------------+
```

### Layer Responsibilities

| Layer | Responsibility | Dependencies |
|-------|---------------|-------------|
| **Domain** | Business entities, rules, port interfaces | None |
| **Application** | Use Cases (business scenarios) | Domain Interfaces |
| **Infrastructure** | Port implementations, DB, external APIs | Doctrine, Symfony |
| **Controllers** | HTTP/CLI adapters, Admin panel | Application Use Cases |

**Important:** The Domain layer has no knowledge of Doctrine, Symfony, or other frameworks. This allows replacing the ORM, database, or even the entire framework without changing business logic.

---

## Project Structure

```
src/
+-- Domain/                         # Domain Layer (Enterprise Logic)
|   +-- Entity/                     # 8 business entities
|   |   +-- Product.php
|   |   +-- Category.php
|   |   +-- Cart.php
|   |   +-- CartItem.php
|   |   +-- Order.php
|   |   +-- OrderItem.php
|   |   +-- Payment.php
|   |   +-- User.php
|   +-- Port/                       # Port Interfaces
|   |   +-- PaymentGatewayInterface.php
|   |   +-- Repository/
|   |       +-- ProductRepositoryInterface.php
|   |       +-- CategoryRepositoryInterface.php
|   |       +-- CartRepositoryInterface.php
|   |       +-- OrderRepositoryInterface.php
|   |       +-- PaymentRepositoryInterface.php
|   +-- Exception/                  # 11 Domain Exceptions
|       +-- DomainException.php
|       +-- CartNotFoundException.php
|       +-- CartItemNotFoundException.php
|       +-- CartEmptyException.php
|       +-- ProductNotFoundException.php
|       +-- ProductNotActiveException.php
|       +-- InsufficientStockException.php
|       +-- OrderNotFoundException.php
|       +-- InvalidOrderStatusException.php
|       +-- PaymentNotFoundException.php
|       +-- WebhookException.php
|
+-- Application/                    # Application Layer (Use Cases)
|   +-- Cart/
|   |   +-- AddItemToCart.php       # Add item to cart
|   |   +-- RemoveItemFromCart.php  # Remove item from cart
|   |   +-- GetCart.php             # Get cart by session
|   |   +-- CheckoutCart.php        # Checkout (create order from cart)
|   +-- Order/
|   |   +-- CreateOrder.php         # Create order
|   |   +-- GetOrderByNumber.php    # Get order by order number
|   |   +-- GetOrdersByEmail.php    # Get orders by email
|   +-- Payment/
|       +-- ProcessPaymentWebhook.php # Process payment webhook
|
+-- Infrastructure/                 # Infrastructure Layer (Adapters)
|   +-- Doctrine/
|   |   +-- Repository/             # 5 Doctrine repository adapters
|   |       +-- ProductDoctrineRepository.php
|   |       +-- CategoryDoctrineRepository.php
|   |       +-- CartDoctrineRepository.php
|   |       +-- OrderDoctrineRepository.php
|   |       +-- PaymentDoctrineRepository.php
|   +-- Payment/
|       +-- Gateway/
|           +-- MockPaymentGateway.php  # Mock payment gateway
|
+-- Controller/                     # HTTP Adapters
|   +-- Api/
|   |   +-- AbstractApiController.php   # Base API controller
|   |   +-- ProductController.php       # Products API
|   |   +-- CategoryController.php      # Categories API
|   |   +-- CartController.php          # Cart API
|   |   +-- OrderController.php         # Orders API
|   |   +-- WebhookController.php       # Webhooks API
|   +-- Admin/                          # EasyAdmin CRUD controllers
|   |   +-- DashboardController.php     # Admin dashboard
|   |   +-- ProductCrudController.php   # Products CRUD
|   |   +-- CategoryCrudController.php  # Categories CRUD
|   |   +-- OrderCrudController.php     # Orders CRUD
|   |   +-- SecurityController.php      # Login page
|   +-- HomeController.php              # Landing page
|
+-- Repository/                     # Symfony security repository
|   +-- UserRepository.php
|
+-- Service/                        # Application services
|   +-- DashboardStatsProvider.php  # Admin dashboard statistics
|   +-- PlaceholderService.php      # SVG placeholder generator
|
+-- DataFixtures/                   # Test data fixtures
|   +-- UserFixtures.php
|   +-- CategoryFixtures.php
|   +-- ProductFixtures.php
|   +-- OrderFixtures.php
|
+-- Kernel.php                      # Symfony Kernel
```

---

## Quick Start

### Requirements

| Component | Version | Notes |
|-----------|---------|-------|
| PHP | 8.2+ | Required |
| Composer | 2.6+ | Dependency manager |
| Docker | 24.0+ | For database containerization |
| Docker Compose | 2.20+ | Container orchestration |

### Installation via Docker

#### 1. Clone repository

```bash
git clone <repository-url> e-commerce-lite
cd e-commerce-lite
```

#### 2. Install dependencies

```bash
composer install
```

#### 3. Configure environment

```bash
cp .env .env.local
```

Edit `.env.local`:

```ini
DATABASE_URL="postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8"
APP_SECRET=your_secret_key_generate_with_openssl
WEBHOOK_SECRET=webhook_test_secret_key_change_in_production
ADMIN_API_KEY=admin_test_key_change_in_production
```

#### 4. Start Docker containers

```bash
docker compose up -d
```

#### 5. Create database and run migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --no-interaction
```

#### 6. Start the server

```bash
symfony server:start
```

Or via PHP built-in server:

```bash
php -S localhost:47000 -t public
```

### Verify

```bash
# API health check
curl http://localhost:47000/api/products

# Database health check
php bin/console doctrine:query:sql "SELECT 1"
```

---

## API Reference

### Products

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | List all products |
| GET | `/api/products/featured?limit=10` | Get featured products |
| GET | `/api/products/{id}` | Get product by ID |

### Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all categories |
| GET | `/api/categories/{id}` | Get category by ID (returns 501) |

### Cart

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cart` | Get current cart |
| POST | `/api/cart/items` | Add item to cart |
| DELETE | `/api/cart/items/{id}` | Remove item from cart |

### Orders

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/orders` | Create order from cart |
| GET | `/api/orders?email=...` | Get orders by email |
| GET | `/api/orders/{orderNumber}` | Get order by number |

### Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/webhooks/payment` | Process payment webhook |
| GET | `/api/webhooks/payment/test` | Test webhook endpoint |

### Admin Panel

| Endpoint | Description |
|----------|-------------|
| `/admin` | Dashboard (requires ROLE_ADMIN) |
| `/admin?entity=Product` | Products CRUD |
| `/admin?entity=Category` | Categories CRUD |
| `/admin?entity=Order` | Orders CRUD |
| `/admin/login` | Login page |

---

## Testing

### Run all tests

```bash
vendor/bin/phpunit
```

### Run by test suite

```bash
# Application layer (unit tests)
vendor/bin/phpunit --testsuite Application

# Functional tests (controllers)
vendor/bin/phpunit --testsuite Functional
```

### Run specific tests

```bash
vendor/bin/phpunit tests/Application/Cart/AddItemToCartTest.php
vendor/bin/phpunit --filter testAddItemToCart
```

### Test structure

```
tests/
+-- Application/                        # Unit tests (Use Cases)
|   +-- Cart/
|   |   +-- AddItemToCartTest.php
|   |   +-- RemoveItemFromCartTest.php
|   |   +-- GetCartTest.php
|   |   +-- CheckoutCartTest.php
|   +-- Order/
|   |   +-- CreateOrderTest.php
|   |   +-- GetOrderByNumberTest.php
|   +-- Payment/
|   |   +-- ProcessPaymentWebhookTest.php
|   +-- Service/
|       +-- DashboardStatsProviderTest.php
+-- Domain/                             # Domain entity tests
|   +-- Entity/
|       +-- CategoryTest.php
|       +-- OrderTest.php
|       +-- OrderItemTest.php
+-- Functional/                         # Integration tests
|   +-- Controller/
|       +-- HomeControllerTest.php
|       +-- Api/
|           +-- ProductControllerTest.php
|           +-- OrderControllerTest.php
|           +-- WebhookControllerTest.php
+-- Traits/
    +-- DatabaseCleanerTrait.php
+-- bootstrap.php
```

---

## Development

### Static Analysis

```bash
# PHPStan (Level 6)
vendor/bin/phpstan analyse

# Rector (check changes)
vendor/bin/rector process --dry-run

# PHP Code Style (PSR-12)
vendor/bin/phpcs src/
vendor/bin/phpcbf src/
```

### Database Migrations

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:migrate prev
php bin/console doctrine:migrations:status
```

### Fixtures

```bash
php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --purge-with-truncate
```

### Docker Utilities

```bash
docker compose restart
docker compose down
docker compose logs database
docker compose exec database psql -U app -d app
```

---

## Project Metrics

| Metric | Value | Status |
|--------|-------|--------|
| Domain entities | 8 | Done |
| Port interfaces | 6 (5 repository + 1 gateway) | Done |
| Use Cases (Application) | 8 | Done |
| Doctrine adapters | 5 | Done |
| Admin CRUD controllers | 5 | Done |
| Domain exceptions | 11 | Done |
| Application tests | 33 | Done |
| Domain tests | 34 | Done |
| Functional tests | 24 | Done |
| Total test methods | 91 | Done |

---

## Known Limitations

1. **Session-based cart**: Uses PHP sessions for cart identification. For production, consider Redis or database-backed sessions.

2. **No authentication for API**: API endpoints do not require authentication. Admin panel requires `ROLE_ADMIN`.

3. **Mock payment gateway**: `MockPaymentGateway` is used instead of a real payment provider. Implement `PaymentGatewayInterface` for production.

4. **WebhookControllerTest**: 7 stub tests (all `markTestIncomplete`). Webhook integration tests need a real or mock payment gateway.

---

## License

**Proprietary** — All rights reserved.

---

## Version

- **Version**: 1.0.0
- **Last updated**: 29 May 2026
- **Status**: Production-ready (with limitations)

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.2-000?logo=symfony)](https://symfony.com)
[![Doctrine](https://img.shields.io/badge/Doctrine-3.3-336699?logo=doctrine)](https://doctrine-project.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?logo=postgresql)](https://postgresql.org)
