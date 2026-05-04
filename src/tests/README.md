# 🧪 Тестирование E-commerce

**Актуальная статистика на 13 марта 2026**

## 📊 Статистика тестов

| Категория | Всего | Пройдено | Assertions |
|-----------|-------|----------|------------|
| **Unit тесты** (Application) | 33 | ✅ 33 (100%) | 118 |
| **Functional тесты** | 24 | ✅ 24 (100%) | 59 |
| **Всего** | **57** | ✅ **57** | **177** |

### Детализация:
- **Failures:** 0 ✅
- **Errors:** 0 ✅
- **Incomplete:** 7 (Webhook тесты требуют WEBHOOK_SECRET)
- **Deprecations:** 1 (не критично)

---

## 🗄️ База данных

### Состояние БД:
- **dev БД** (`var/data.db`) — 116K, 13 товаров, 15 заказов
- **test БД** (`var/data.test.db`) — 116K, идентична dev

### Синхронизация БД:
```bash
# Синхронизировать test БД с dev (фикстуры + копирование)
./tests/sync-db.sh
```

**Что делает скрипт:**
1. Загружает фикстуры в dev БД
2. Копирует dev БД в test БД
3. Обе БД становятся идентичными

---

## 🧪 Запуск тестов

### Все тесты:
```bash
./tests/run-tests.sh
```

### С опциями PHPUnit:
```bash
# Подробный вывод
./tests/run-tests.sh --testdox

# Только Unit тесты (Application suite)
./tests/run-tests.sh --testsuite Application

# Только Functional тесты
./tests/run-tests.sh --testsuite Functional

# Конкретный тест
./tests/run-tests.sh --filter testGetStats
./tests/run-tests.sh --filter testCreateOrder
```

### Напрямую через Docker:
```bash
docker exec shop-php-fpm-1 php /application/bin/phpunit --testdox
```

---

## 📁 Структура тестов

```
tests/
├── Application/                    # Unit тесты (33 теста)
│   ├── Cart/
│   │   ├── AddItemToCartTest.php
│   │   ├── CheckoutCartTest.php
│   │   ├── GetCartTest.php
│   │   └── RemoveItemFromCartTest.php
│   ├── Order/
│   │   ├── CreateOrderTest.php
│   │   └── GetOrderByNumberTest.php
│   ├── Payment/
│   │   └── ProcessPaymentWebhookTest.php
│   └── Service/
│       └── DashboardStatsProviderTest.php (8 тестов)
│
├── Domain/
│   └── Entity/                     # Unit тесты сущностей (14 тестов)
│       ├── OrderTest.php           (14 тестов)
│       ├── OrderItemTest.php       (10 тестов)
│       └── CategoryTest.php        (10 тестов)
│
├── Functional/
│   └── Controller/                 # Functional тесты (24 теста)
│       ├── Api/
│       │   ├── OrderControllerTest.php     (7 тестов)
│       │   └── ProductControllerTest.php   (5 тестов)
│       └── HomeControllerTest.php          (4 теста)
│
├── Traits/
│   └── DatabaseCleanerTrait.php    # Очистка БД между тестами
│
├── bootstrap.php                   # Инициализация тестов
├── sync-db.sh                      # Синхронизация БД
├── run-tests.sh                    # Запуск тестов
└── README.md                       # Этот файл
```

---

## 🔧 DatabaseCleanerTrait

Трейт автоматически очищает БД **перед** и **после** каждого Functional теста.

### Использование:
```php
use App\Tests\Traits\DatabaseCleanerTrait;

class OrderControllerTest extends WebTestCase
{
    use DatabaseCleanerTrait;
    
    // ... тесты
}
```

### Механизм работы:
```
┌─────────────────────────────────────┐
│  Тест: testCreateOrder()            │
├─────────────────────────────────────┤
│  1. setUp()                         │
│     └─> cleanDatabase() ← Очистка   │
│     └─> parent::setUp()             │
│                                     │
│  2. testCreateOrder() ← ТЕСТ        │
│     (создаёт данные, проверяет)     │
│                                     │
│  3. tearDown()                      │
│     └─> cleanDatabase() ← Очистка   │
│     └─> parent::tearDown()          │
└─────────────────────────────────────┘
```

### Метод cleanDatabase():
1. Отключает `PRAGMA foreign_keys = OFF` (SQLite)
2. Получает список всех таблиц
3. Выполняет `DELETE FROM table` для каждой
4. Включает `PRAGMA foreign_keys = ON`

**Важно:** Unit тесты не используют трейт (работают с моками).

---

## 📝 Рекомендации

### Перед запуском тестов:
```bash
# 1. Синхронизировать БД (если были изменения в фикстурах)
./tests/sync-db.sh

# 2. Запустить тесты
./tests/run-tests.sh --testdox
```

### Добавление нового теста:

**Unit тест:**
1. Создаём файл в `tests/Application/` или `tests/Domain/`
2. Расширяем `TestCase`
3. Используем моки для зависимостей

**Functional тест:**
1. Создаём файл в `tests/Functional/`
2. Расширяем `WebTestCase`
3. Добавляем `use DatabaseCleanerTrait;`
4. Пишем тесты с HTTP запросами

### Если тесты падают:
```bash
# 1. Проверить что БД синхронизирована
./tests/sync-db.sh

# 2. Запустить конкретный тест
./tests/run-tests.sh --filter testName

# 3. Посмотреть детали ошибки
./tests/run-tests.sh --filter testName --verbose

# 4. Проверить логи
docker logs shop-php-fpm-1 | tail -50
```

---

## 🎯 Покрытие тестами

### Application слой (100%):
- ✅ Cart: AddItem, Checkout, GetCart, RemoveItem
- ✅ Order: CreateOrder, GetOrderByNumber
- ✅ Payment: ProcessPaymentWebhook
- ✅ Service: DashboardStatsProvider (8 тестов)

### Domain слой (100%):
- ✅ Order (14 тестов)
- ✅ OrderItem (10 тестов)
- ✅ Category (10 тестов)

### Functional тесты:
- ✅ HomeController (4 теста)
- ✅ OrderController API (7 тестов)
- ✅ ProductController API (5 тестов)
- ⏸️ WebhookController (7 тестов incomplete)

---

## 🔗 Полезные ссылки

- [PHPUnit документация](https://phpunit.de/manual/current/ru/index.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [DatabaseCleanerTrait](tests/Traits/DatabaseCleanerTrait.php)
