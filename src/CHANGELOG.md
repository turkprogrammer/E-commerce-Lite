# 📝 Changelog

Все заметные изменения в проекте E-commerce Lite будут задокументированы в этом файле.

Формат основан на [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/),
проект следует [Semantic Versioning](https://semver.org/lang/ru/).

---

## [1.0.0] — 2026-03-13

### ✨ Добавлено

#### Архитектура
- **Гексагональная архитектура** (Ports & Adapters) реализована
- **Domain слой** — 7 чистых сущностей без зависимостей
  - `Product`, `Category`, `Cart`, `CartItem`, `Order`, `OrderItem`, `Payment`
- **Application слой** — 5 Use Case
  - `AddItemToCart`, `RemoveItemFromCart`, `GetCart`
  - `CreateOrder`, `GetOrdersByEmail`
- **Domain Ports** — 4 интерфейса репозиториев
  - `ProductRepositoryInterface`, `CategoryRepositoryInterface`
  - `CartRepositoryInterface`, `OrderRepositoryInterface`
- **Infrastructure адаптеры** — 4 Doctrine репозитория
  - `ProductDoctrineRepository`, `CategoryDoctrineRepository`
  - `CartDoctrineRepository`, `OrderDoctrineRepository`
- **Attribute Mapping** для всех 7 сущностей (PHP 8 атрибуты)

#### Контроллеры
- `ProductController` — API для товаров (GET /api/products, /api/products/featured)
- `CategoryController` — API для категорий (GET /api/categories)
- `CartController` — CRUD корзины на Use Case
- `OrderController` — Создание и список заказов

#### Тесты
- **57 тестов** (177 assertions) — 100% passing
- **33 Unit теста** для Application и Domain слоя
- **24 Functional теста** для контроллеров
- TDD workflow (Red → Green → Refactor)
- DatabaseCleanerTrait для очистки БД
- Скрипты: `run-tests.sh`, `sync-db.sh`

#### Инструменты
- **PHPStan** Level 9 настроен
- **Rector** для автоматического рефакторинга
- **Docker Compose** с PostgreSQL 16
- **Symfony Maker** для генерации кода

#### Документация
- `README.md` — полная документация проекта
- `CONTRIBUTING.md` — руководство для контрибьюторов
- `HEXAGONAL_COMPLETE.md` — отчёт о миграции
- `tests/README.md` — руководство по тестированию

### 🔧 Изменено

- Контроллеры переписаны на использование Use Case
- Doctrine аннотации заменены на XML mapping
- Entity удалены из `src/Entity/` в `src/Domain/Entity/`
- Repository удалены из `src/Repository/` в `src/Infrastructure/Doctrine/Repository/`
- Service удалены (полностью)
- Маршруты API упорядочены (featured перед {id})

### 🗑️ Удалено

- `src/Entity/*` — перемещено в Domain
- `src/Repository/*` — перемещено в Infrastructure
- `src/Service/*Service.php` — удалено (CartService, OrderService, PaymentService)
- `src/DTO/*` — удалено (AddToCartRequest, CreateOrderRequest, UpdateCartRequest)
- `src/Trait/PriceCalculationTrait.php` — удалено
- `src/Serializer/CircularReferenceHandler.php` — удалено
- Старые тесты Entity и Service
- `tests/init-test-db.sh` — заменён на `sync-db.sh`

### ✅ Исправлено

- Маршрут `/api/products/featured` больше не перехватывается `/api/products/{id}`
- Все Functional тесты проходят (24/24)
- DatabaseCleanerTrait корректно очищает БД между тестами
- Тестовая БД синхронизирована с dev (фикстуры + копирование)

### ⚠️ Известные ограничения

- Все контроллеры реализованы ✅
- Webhook тесты требуют WEBHOOK_SECRET (7 incomplete)

---

## [0.9.0] — 2026-03-10

### ✨ Добавлено

- Начальная версия проекта
- Базовая структура Symfony 7.2
- Doctrine ORM с аннотациями
- CRUD операции для товаров и категорий

### 🔧 Изменено

- Конфигурация по умолчанию для Symfony

---

## [Unreleased]

### В плане

- Интеграция с платёжными системами (Stripe, PayPal)
- Аутентификация (JWT/OAuth2)
- Админ-панель для управления заказами
- Email уведомления о заказах
- GraphQL API
- Event Sourcing для заказов
- CQRS для чтения/записи
- Кэширование (Redis)
- Поиск товаров (Elasticsearch)

---

## 📋 Версии

| Версия | Дата | Статус | Описание |
|--------|------|--------|----------|
| [1.0.0] | 2026-03-13 | ✅ Release | Гексагональная архитектура + 57 тестов |
| [0.9.0] | 2026-03-10 | 🏁 Legacy | Начальная версия |

---

## 🎯 Критерии приемки v1.0.0

| Критерий | Статус |
|----------|--------|
| Нет `src/Entity/` | ✅ |
| Нет `src/Service/*Service.php` | ✅ |
| Нет `src/Repository/*` | ✅ |
| Контроллеры на Use Case | ✅ |
| Domain без зависимостей | ✅ |
| Application без Doctrine | ✅ |
| XML mapping работает | ✅ |
| Тесты проходят (57/57) | ✅ (177 assertions) |
| Functional тесты (24/24) | ✅ |
| Unit тесты (33/33) | ✅ |
| Маршруты API исправлены | ✅ |

---

## 📞 Контакты

- **Релиз v1.0.0**: 13 марта 2026
- **Статус**: ✅ Готов к production (с ограничениями)
- **Тесты**: ✅ 57 тестов, 177 assertions (100% passing)

---

<div align="center">

**E-commerce Lite** — чистая архитектура для современной e-commerce платформы

[1.0.0]: https://github.com/your-repo/e-commerce-lite/releases/tag/v1.0.0
[0.9.0]: https://github.com/your-repo/e-commerce-lite/releases/tag/v0.9.0

</div>
