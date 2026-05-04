# Анализ архитектур для миграции с MVC

## 📊 Текущее состояние

**Архитектура:** Классический MVC (Symfony Monolith)
**Проблемы:**
- Тесная связанность с Symfony Framework
- Бизнес-логика частично в контроллерах, частично в сервисах
- Сложность тестирования без контейнера Symfony
- Зависимость от Doctrine ORM в бизнес-логике

## 🎯 Кандидаты на миграцию

### 1. Чистая архитектура (Clean Architecture)

```
┌─────────────────────────────────────────────────────┐
│                 Frameworks & Drivers                │
│  (Symfony, Doctrine, Twig, Controllers, Tests)     │
└─────────────────────────────────────────────────────┘
                        ↕ Uses
┌─────────────────────────────────────────────────────┐
│              Interface Adapters                     │
│  (Presenters, Gateways, Repository Implementations)│
└─────────────────────────────────────────────────────┘
                        ↕ Uses
┌─────────────────────────────────────────────────────┐
│              Use Cases (Application Logic)          │
│  (AddProductToCart, CreateOrder, ProcessPayment)   │
└─────────────────────────────────────────────────────┘
                        ↕ Uses
┌─────────────────────────────────────────────────────┐
│                 Entities (Enterprise Logic)         │
│  (Product, Cart, Order - чистые объекты PHP)       │
└─────────────────────────────────────────────────────┘
```

**Плюсы:**
- ✅ Полная независимость от фреймворков
- ✅ Бизнес-логика в Use Cases (не в Controllers)
- ✅ Entities без зависимостей (чистый PHP)
- ✅ Легкая замена Symfony на другой фреймворк
- ✅ Тестирование без контейнера Symfony

**Минусы:**
- ❌ Требует рефакторинга ВСЕГО кода
- ❌ Много boilerplate-кода (Interfaces, DTOs)
- ❌ Сложность для команды (нужно изучать паттерны)
- ❌ Избыточно для малого проекта

**Оценка миграции:** 🔴 **Очень болезненно** (40-80 часов)

---

### 2. Гексагональная архитектура (Ports & Adapters)

```
┌─────────────────────────────────────────────────────┐
│            Primary Adapters (Driving)               │
│  (HTTP Controllers, CLI Commands, Tests)           │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│                 Ports (Interfaces)                  │
│  (RepositoryInterface, PaymentGatewayInterface)    │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│           Application Core (Domain Logic)           │
│  (Services + Domain Entities + Use Cases)          │
└─────────────────────────────────────────────────────┘
                        ↕
┌─────────────────────────────────────────────────────┐
│            Secondary Adapters (Driven)              │
│  (Doctrine Repositories, External APIs, DB)        │
└─────────────────────────────────────────────────────┘
```

**Плюсы:**
- ✅ Явное разделение на «вход» и «выход»
- ✅ Бизнес-логика изолирована в ядре
- ✅ Легко моковать зависимости (тесты)
- ✅ Можно постепенно внедрять
- ✅ Doctrine только в адаптерах (не в ядре)

**Минусы:**
- ❌ Требует введения интерфейсов (RepositoryInterface)
- ❌ Больше файлов (адаптеры, порты)
- ⚠️ Может быть over-engineering для CRUD

**Оценка миграции:** 🟡 **Умеренно болезненно** (20-40 часов)

---

### 3. Модульный монолит (Modular Monolith)

```
src/
├── Module/
│   ├── Catalog/
│   │   ├── Application/  (Use Cases)
│   │   ├── Domain/       (Entities, Interfaces)
│   │   └── Infrastructure/ (Doctrine, Controllers)
│   ├── Cart/
│   │   ├── Application/
│   │   ├── Domain/
│   │   └── Infrastructure/
│   ├── Order/
│   │   ├── Application/
│   │   ├── Domain/
│   │   └── Infrastructure/
│   └── Payment/
│       ├── Application/
│       ├── Domain/
│       └── Infrastructure/
```

**Плюсы:**
- ✅ Четкие границы контекстов (DDD Bounded Context)
- ✅ Можно рефакторить по одному модулю
- ✅ Масштабируемость (выделение в микросервисы)
- ✅ Команды работают независимо
- ✅ Сохраняем Symfony в Infrastructure

**Минусы:**
- ❌ Сложная структура папок
- ❌ Межмодульная коммуникация (Events?)
- ❌ Дублирование кода между модулями
- ⚠️ Требует дисциплины команды

**Оценка миграции:** 🟡 **Умеренно болезненно** (30-50 часов)

---

### 4. Вертикальный срез (Vertical Slice Architecture)

```
src/
├── Features/
│   ├── Products/
│   │   ├── ListProducts/
│   │   │   ├── ListProductsController.php
│   │   │   ├── ListProductsHandler.php
│   │   │   └── ListProductsRequest.php
│   │   ├── ShowProduct/
│   │   └── CreateProduct/
│   ├── Cart/
│   │   ├── AddItemToCart/
│   │   ├── RemoveItemFromCart/
│   │   └── CheckoutCart/
│   └── Orders/
│       ├── CreateOrder/
│       └── CancelOrder/
```

**Плюсы:**
- ✅ Каждая фича изолирована (нет cross-cutting)
- ✅ Нет общих моделей (каждая фича имеет свою)
- ✅ Легко удалять фичи (просто удалить папку)
- ✅ Нет coupled layers
- ✅ Можно делать постепенно

**Минусы:**
- ❌ Дублирование кода (DTO, валидация)
- ❌ Сложнее найти код (нет единой Entity/)
- ❌ Не подходит для CRUD-интенсивных проектов
- ⚠️ Фрагментация доменной модели

**Оценка миграции:** 🟢 **Наименее болезненно** (15-30 часов)

---

## 📈 Сравнительная таблица

| Критерий | Чистая | Гексагональная | Модульная | Вертикальная |
|----------|--------|----------------|-----------|--------------|
| **Сложность миграции** | 🔴 Высокая | 🟡 Средняя | 🟡 Средняя | 🟢 Низкая |
| **Изоляция бизнес-логики** | ✅ Отличная | ✅ Отличная | ✅ Хорошая | ⚠️ Частичная |
| **Тестируемость** | ✅ Отличная | ✅ Отличная | ✅ Хорошая | ✅ Хорошая |
| **Гибкость замены БД** | ✅ Полная | ✅ Полная | ⚠️ Частичная | ⚠️ Частичная |
| **Порог входа для команды** | 🔴 Высокий | 🟡 Средний | 🟡 Средний | 🟢 Низкий |
| **Подходит для e-commerce** | ⚠️ Overkill | ✅ Да | ✅ Да | ⚠️ Для CRUD |
| **Время миграции** | 40-80ч | 20-40ч | 30-50ч | 15-30ч |

---

## 🏆 Рекомендация для E-commerce Lite

### **Выбор: Гексагональная архитектура (Ports & Adapters)**

**Почему:**

1. **Безболезненная миграция:**
   - Можно внедрять постепенно (по одному модулю)
   - Не требует переписывания всего кода
   - Сохраняем Symfony в Controllers

2. **Решает текущие проблемы:**
   - ✅ Изоляция бизнес-логики от Doctrine
   - ✅ Легкое тестирование через моки
   - ✅ Явные зависимости (через интерфейсы)

3. **Оптимальный баланс:**
   - Не over-engineering как Clean Architecture
   - Не fragmented как Vertical Slice
   - Более явная чем Modular Monolith

4. **Подходит для e-commerce:**
   - Четкое разделение на «вход» (HTTP, CLI) и «выход» (БД, API)
   - Легко добавлять новые каналы (GraphQL, Mobile API)
   - Можно заменить Doctrine на другую ORM

---

## 🗺️ План миграции на гексагональную архитектуру

### Этап 1: Подготовка (2-4 часа)
```
✓ Создать папку src/Domain/
✓ Создать папку src/Application/
✓ Создать папку src/Infrastructure/
```

### Этап 2: Выделение домена (8-12 часов)
```
✓ Переместить Entities в Domain/
✓ Удалить Doctrine annotations из Entities
✓ Создать Repository Interfaces в Domain/
```

### Этап 3: Создание Use Cases (8-16 часов)
```
✓ Создать Use Case для Cart (AddItem, RemoveItem, Checkout)
✓ Создать Use Case для Order (CreateOrder, CancelOrder)
✓ Создать Use Case для Product (ListProducts, ShowProduct)
```

### Этап 4: Адаптеры (4-8 часов)
```
✓ Переместить Controllers в Infrastructure/
✓ Создать Doctrine Repository Implementations
✓ Обновить dependency injection
```

### Этап 5: Тесты (4-6 часов)
```
✓ Обновить Unit тесты (без Symfony Kernel)
✓ Обновить Integration тесты (с моками)
```

**Итого: 26-46 часов (~1 неделя для 1 разработчика)**

---

## 📝 Пример кода (до/после)

### ДО (MVC):
```php
// src/Controller/Api/CartController.php
class CartController extends AbstractController
{
    #[Route('/api/cart/items', methods: ['POST'])]
    public function addItem(Request $request, EntityManagerInterface $em): Response
    {
        $product = $em->getRepository(Product::class)->find($request->get('productId'));
        // ... логика смешана с инфраструктурой
    }
}
```

### ПОСЛЕ (Гексагональная):
```php
// src/Application/Cart/AddItemToCartHandler.php
class AddItemToCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepo,
        private ProductRepositoryInterface $productRepo,
    ) {}
    
    public function handle(AddItemToCartCommand $command): Cart
    {
        // Чистая бизнес-логика без зависимостей на БД
    }
}

// src/Infrastructure/Controller/AddItemToCartController.php
class AddItemToCartController extends AbstractController
{
    public function __invoke(
        AddItemToCartRequest $request,
        AddItemToCartHandler $handler,
    ): JsonResponse {
        $cart = $handler->handle($request->toCommand());
        return $this->json($cart);
    }
}
```

---

## ⚠️ Риски и mitigation

| Риск | Mitigation |
|------|------------|
| Команда не поймет архитектуру | Провести workshop, создать documentation |
| Миграция затянется | Делать итеративно (по модулю в спринт) |
| Усложнение кода | Начать с критичных модулей (Cart, Order) |
| Падение производительности | Профилировать, добавить caching |

---

## ✅ Вывод

**Гексагональная архитектура** — оптимальный выбор для E-commerce Lite:
- ✅ Безболезненная миграция (20-40 часов)
- ✅ Лучшая тестируемость
- ✅ Изоляция бизнес-логики
- ✅ Гибкость для будущих изменений

**Чистая архитектура** — overkill для текущего масштаба проекта.

**Вертикальный срез** — хорош для CRUD, но не подходит для сложной бизнес-логики (корзина, заказы, оплата).

---

## 📝 Статус миграции (обновлено 13 марта 2026)

### ✅ Миграция завершена!

**Статус**: ✅ **ПОЛНОСТЬЮ РЕАЛИЗОВАНО**

**Результаты:**
- ✅ Гексагональная архитектура внедрена
- ✅ Domain слой без зависимостей (7 сущностей)
- ✅ Application слой с Use Case (6 сервисов)
- ✅ Infrastructure адаптеры (4 репозитория)
- ✅ XML mapping для всех сущностей
- ✅ 57 тестов (177 assertions) — 100% passing
- ✅ Все контроллеры на Use Case

**Документация:**
- См. [HEXAGONAL_COMPLETE.md](HEXAGONAL_COMPLETE.md) — отчёт о завершении
- См. [tests/README.md](tests/README.md) — руководство по тестированию
- См. [README.md](README.md) — основная документация

**Дата завершения**: 13 марта 2026 г.
