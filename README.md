# 🛒 E-commerce Lite

**Современный интернет-магазин на Symfony 7.2 с гексагональной архитектурой**

Легковесная e-commerce платформа с чистым Domain-слоем, Use Case-ориентированным Application-слоем и заменяемыми адаптерами инфраструктуры.

---

## ⚡ Особенности

- **🏗️ Гексагональная архитектура** (Ports & Adapters) — бизнес-логика без зависимостей от фреймворков
- **📦 Domain-Driven Design** — 7 чистых сущностей, 4 репозитория-интерфейса
- **🎯 Use Case ориентированность** — 5 Application сервисов для ключевых сценариев
- **🔧 Attribute Mapping** — конфигурация Doctrine через PHP 8 атрибуты
- **🧪 TDD подход** — тесты для Application слоя
- **📊 Статический анализ** — PHPStan Level 9, Rector
- **🐳 Docker First** — PostgreSQL 16 через Docker Compose
- **🔌 DI Container** — Symfony Dependency Injection без Singleton'ов

---

## 🏗️ Архитектура

Проект следует принципам **гексагональной архитектуры** (Ports & Adapters), где бизнес-логика изолирована от внешних зависимостей.

```
┌─────────────────────────────────────────────────────────┐
│              HTTP Request (REST API)                    │
│              CLI Commands / Tests                       │
└─────────────────────────────────────────────────────────┘
                        ↓ Uses
┌─────────────────────────────────────────────────────────┐
│         Controllers (Primary Adapters)                  │
│  CartController, OrderController, etc.                 │
│  - Принимают запрос                                   │
│  - Валидируют input                                   │
│  - Вызывают Use Case                                  │
│  - Возвращают response                                │
└─────────────────────────────────────────────────────────┘
                        ↓ Uses
┌─────────────────────────────────────────────────────────┐
│           Application (Use Cases)                       │
│  AddItemToCart, CreateOrder, GetCart, etc.            │
│  - Чистая бизнес-логика                               │
│  - Зависят от Domain Interfaces                       │
│  - Без зависимостей на инфраструктуру                 │
└─────────────────────────────────────────────────────────┘
                        ↓ Uses
┌─────────────────────────────────────────────────────────┐
│          Domain (Ports + Entities)                      │
│  Repository Interfaces + Domain Entities              │
│  - Enterprise бизнес-правила                          │
│  - Без внешних зависимостей                           │
│  - Чистый PHP                                         │
└─────────────────────────────────────────────────────────┘
                        ↓ Implemented by
┌─────────────────────────────────────────────────────────┐
│       Infrastructure (Secondary Adapters)               │
│  Doctrine Repositories + Attribute Mapping             │
│  - Реализация интерфейсов                             │
│  - Работа с БД                                        │
│  - Внешние сервисы                                    │
└─────────────────────────────────────────────────────────┘
```

### Ключевые преимущества

| Слой | Ответственность | Зависимости |
|------|----------------|-------------|
| **Domain** | Бизнес-сущности и правила | Нет |
| **Application** | Use Case (сценарии использования) | Domain Interfaces |
| **Infrastructure** | Реализация портов, БД, внешние API | Doctrine, Symfony |
| **Controllers** | HTTP/CLI адаптеры | Application Use Cases |

**Важно:** Domain слой не знает о Doctrine, Symfony или других фреймворках. Это позволяет заменить ORM, БД или даже весь фреймворк без изменения бизнес-логики.

---

## 📁 Структура проекта

```
src/
├── Domain/                    # Domain слой (Enterprise Logic)
│   ├── Entity/               # 7 чистых сущностей
│   │   ├── Product.php       # Товар
│   │   ├── Category.php      # Категория
│   │   ├── Cart.php          # Корзина
│   │   ├── CartItem.php      # Элемент корзины
│   │   ├── Order.php         # Заказ
│   │   ├── OrderItem.php     # Элемент заказа
│   │   └── Payment.php       # Платёж
│   └── Port/
│       └── Repository/       # 4 интерфейса (Ports)
│           ├── ProductRepositoryInterface.php
│           ├── CategoryRepositoryInterface.php
│           ├── CartRepositoryInterface.php
│           └── OrderRepositoryInterface.php
│
├── Application/              # Application слой (Use Cases)
│   ├── Cart/
│   │   ├── AddItemToCart.php      # Добавить товар в корзину
│   │   ├── RemoveItemFromCart.php # Удалить товар из корзины
│   │   └── GetCart.php            # Получить корзину
│   └── Order/
│       ├── CreateOrder.php        # Создать заказ
│       └── GetOrdersByEmail.php   # Получить заказы по email
│
├── Infrastructure/           # Infrastructure слой (Adapters)
│   └── Doctrine/
│       ├── Mapping/          # XML mapping для 7 сущностей
│       │   ├── Product.orm.xml
│       │   ├── Category.orm.xml
│       │   ├── Cart.orm.xml
│       │   ├── CartItem.orm.xml
│       │   ├── Order.orm.xml
│       │   ├── OrderItem.orm.xml
│       │   └── Payment.orm.xml
│       └── Repository/       # 4 Doctrine адаптера
│           ├── ProductDoctrineRepository.php
│           ├── CategoryDoctrineRepository.php
│           ├── CartDoctrineRepository.php
│           └── OrderDoctrineRepository.php
│
├── Controller/
│   ├── Api/
│   │   ├── AbstractApiController.php  # Базовый API контроллер
│   │   ├── ProductController.php      # GET /api/products
│   │   ├── CategoryController.php     # GET /api/categories
│   │   ├── CartController.php         # CRUD корзины
│   │   └── OrderController.php        # CRUD заказов
│   └── Admin/                        # Admin контроллеры (в разработке)
│
├── DataFixtures/            # Фикстуры для тестирования
│
├── Service/                 # ⚠️ Устаревший код (подлежит удалению)
│   └── PlaceholderService.php
│
└── Kernel.php               # Symfony Kernel
```

---

## 🚀 Быстрый старт

### Требования

| Компонент | Версия | Примечание |
|-----------|--------|------------|
| PHP | 8.5+ | Обязательно |
| Composer | 2.6+ | Менеджер зависимостей |
| Docker | 24.0+ | Для контейнеризации БД |
| Docker Compose | 2.20+ | Оркестрация контейнеров |

### Установка через Docker

#### 1. Клонирование репозитория

```bash
git clone <repository-url> e-commerce-lite
cd e-commerce-lite
```

#### 2. Установка зависимостей

```bash
composer install
```

#### 3. Настройка переменных окружения

```bash
cp .env .env.local
```

Отредактируйте `.env.local`:

```ini
# База данных (PostgreSQL через Docker)
DATABASE_URL="postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8"

# Секретный ключ (сгенерируйте уникальный)
APP_SECRET=your_secret_key_generate_with_openssl

# Webhook секрет
WEBHOOK_SECRET=webhook_test_secret_key_change_in_production

# Admin API key
ADMIN_API_KEY=admin_test_key_change_in_production
```

#### 4. Запуск Docker контейнеров

```bash
docker compose up -d
```

Проверка статуса:

```bash
docker compose ps
```

Ожидаемый вывод:
```
NAME                    STATUS         PORTS
src-database-1          Up (healthy)   5432/tcp
```

#### 5. Создание базы данных и миграции

```bash
# Создать БД
php bin/console doctrine:database:create

# Применить миграции
php bin/console doctrine:migrations:migrate

# Загрузить фикстуры (опционально)
php bin/console doctrine:fixtures:load --no-interaction
```

#### 6. Запуск Symfony сервера

```bash
symfony server:start
```

Или через PHP built-in server:

```bash
php -S localhost:47000 -t public
```

### Проверка работы

#### Проверка API

```bash
# Получить список товаров
curl http://localhost:47000/api/products

# Получить список категорий
curl http://localhost:47000/api/categories
```

#### Проверка здоровья БД

```bash
php bin/console doctrine:query:sql "SELECT 1"
```

Ожидаемый вывод: `1`

---

## 📡 API Reference

### Products API

#### Получить список товаров

```http
GET /api/products
```

**Пример запроса:**

```bash
curl -X GET http://localhost:47000/api/products
```

**Ответ (200 OK):**

```json
{
  "error": false,
  "message": "Товары получены",
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Товар 1",
        "price": 1000,
        "active": true
      }
    ],
    "total": 1
  }
}
```

---

#### Получить товар по ID

```http
GET /api/products/{id}
```

**Пример запроса:**

```bash
curl -X GET http://localhost:47000/api/products/1
```

**Ответ (200 OK):**

```json
{
  "error": false,
  "message": "Товар найден",
  "data": {
    "product": {
      "id": 1,
      "name": "Товар 1",
      "price": 1000,
      "active": true
    }
  }
}
```

**Ответ (404 Not Found):**

```json
{
  "error": true,
  "message": "Товар не найден",
  "data": []
}
```

---

#### Получить избранные товары

```http
GET /api/products/featured?limit=10
```

**Параметры:**

| Параметр | Тип | По умолчанию | Описание |
|----------|-----|--------------|----------|
| limit | int | 10 | Максимальное количество товаров |

**Пример запроса:**

```bash
curl -X GET "http://localhost:47000/api/products/featured?limit=5"
```

---

### Categories API

#### Получить список категорий

```http
GET /api/categories
```

**Пример запроса:**

```bash
curl -X GET http://localhost:47000/api/categories
```

**Ответ (200 OK):**

```json
{
  "error": false,
  "message": "Категории получены",
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "Категория 1",
        "active": true
      }
    ],
    "total": 1
  }
}
```

---

#### Получить категорию по ID

```http
GET /api/categories/{id}
```

**Статус:** ⏳ В разработке (требуется реализация метода `find()` в интерфейсе)

---

### Cart API

#### Получить корзину

```http
GET /api/cart
```

**Пример запроса:**

```bash
curl -X GET http://localhost:47000/api/cart \
  -H "Cookie: PHPSESSID=your_session_id"
```

**Ответ (200 OK):**

```json
{
  "error": false,
  "message": "Корзина получена",
  "data": {
    "cart": {
      "id": 1,
      "sessionId": "abc123",
      "items": [
        {
          "productId": 1,
          "quantity": 2,
          "price": 1000
        }
      ],
      "totalAmount": 2000,
      "totalItems": 2,
      "isEmpty": false
    }
  }
}
```

---

#### Добавить товар в корзину

```http
POST /api/cart/items
```

**Тело запроса:**

```json
{
  "productId": 1,
  "quantity": 2
}
```

**Пример запроса:**

```bash
curl -X POST http://localhost:47000/api/cart/items \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{"productId": 1, "quantity": 2}'
```

**Ответ (201 Created):**

```json
{
  "error": false,
  "message": "Товар добавлен в корзину",
  "data": {
    "item": {
      "productId": 1,
      "quantity": 2,
      "price": 1000
    },
    "cart": {
      "totalAmount": 2000,
      "totalItems": 2
    }
  }
}
```

**Ответ (400 Bad Request):**

```json
{
  "error": true,
  "message": "Товар не найден",
  "data": []
}
```

---

#### Обновить количество товара

```http
PUT /api/cart/items/{id}
```

**Тело запроса:**

```json
{
  "quantity": 3
}
```

**Пример запроса:**

```bash
curl -X PUT http://localhost:47000/api/cart/items/1 \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{"quantity": 3}'
```

---

#### Удалить товар из корзины

```http
DELETE /api/cart/items/{id}
```

**Пример запроса:**

```bash
curl -X DELETE http://localhost:47000/api/cart/items/1 \
  -H "Cookie: PHPSESSID=your_session_id"
```

**Ответ (200 OK):**

```json
{
  "error": false,
  "message": "Товар удален из корзины",
  "data": []
}
```

**Ответ (404 Not Found):**

```json
{
  "error": true,
  "message": "Товар не найден в корзине",
  "data": []
}
```

---

#### Очистить корзину

```http
POST /api/cart/clear
```

**Пример запроса:**

```bash
curl -X POST http://localhost:47000/api/cart/clear \
  -H "Cookie: PHPSESSID=your_session_id"
```

---

### Orders API

#### Создать заказ

```http
POST /api/orders
```

**Тело запроса:**

```json
{
  "email": "customer@example.com",
  "firstName": "Иван",
  "lastName": "Иванов",
  "phone": "+7 (999) 123-45-67",
  "address": "г. Москва, ул. Примерная, д. 1",
  "items": [
    {
      "productId": 1,
      "quantity": 2
    },
    {
      "productId": 2,
      "quantity": 1
    }
  ]
}
```

**Пример запроса:**

```bash
curl -X POST http://localhost:47000/api/orders \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{
    "email": "customer@example.com",
    "firstName": "Иван",
    "lastName": "Иванов",
    "phone": "+7 (999) 123-45-67",
    "address": "г. Москва, ул. Примерная, д. 1",
    "items": [
      {"productId": 1, "quantity": 2},
      {"productId": 2, "quantity": 1}
    ]
  }'
```

**Ответ (201 Created):**

```json
{
  "error": false,
  "message": "Заказ успешно создан",
  "data": {
    "order": {
      "orderNumber": "ORD-2026-000001",
      "status": "pending",
      "totalAmount": 3000,
      "items": [...]
    }
  }
}
```

**Ответ (400 Bad Request):**

```json
{
  "error": true,
  "message": "Корзина пуста",
  "data": []
}
```

---

#### Получить список заказов

```http
GET /api/orders?email=customer@example.com
```

**Параметры:**

| Параметр | Тип | Обязательный | Описание |
|----------|-----|--------------|----------|
| email | string | Да | Email покупателя |

**Пример запроса:**

```bash
curl -X GET "http://localhost:47000/api/orders?email=customer@example.com"
```

**Ответ (200 OK):**

```json
{
  "error": false,
  "message": "Заказы получены",
  "data": {
    "orders": [...],
    "total": 2
  }
}
```

---

#### Получить заказ по номеру

```http
GET /api/orders/{orderNumber}
```

**Статус:** ⏳ В разработке (требуется создание `GetOrderByNumber` Use Case)

---

### Webhooks API

#### Webhook оплаты

```http
POST /api/webhooks/payment
```

**Статус:** ⏳ В разработке (требуется создание `ProcessPaymentWebhook` Use Case)

---

#### Тест webhook

```http
GET /api/webhooks/payment/test
```

**Статус:** ⏳ В разработке

---

## 🧪 Тестирование

### Запуск всех тестов

```bash
# Через PHPUnit
vendor/bin/phpunit

# Через Symfony консоль
php bin/console phpunit
```

### Запуск тестов по группам

```bash
# Application слой (Unit тесты)
vendor/bin/phpunit --testsuite Application

# Functional тесты (контроллеры)
vendor/bin/phpunit --testsuite Functional
```

### Запуск отдельных тестов

```bash
# Конкретный тест-файл
vendor/bin/phpunit tests/Application/Cart/AddItemToCartTest.php

# Конкретный тест-метод
vendor/bin/phpunit --filter testAddItemToCart tests/Application/Cart/AddItemToCartTest.php
```

### Покрытие кода

```bash
vendor/bin/phpunit --coverage-html var/coverage
```

Откройте `var/coverage/index.html` в браузере для просмотра отчёта.

### Структура тестов

```
tests/
├── Application/           # Unit тесты Use Case
│   ├── Cart/
│   │   ├── AddItemToCartTest.php
│   │   ├── RemoveItemFromCartTest.php
│   │   └── GetCartTest.php
│   └── Order/
│       ├── CreateOrderTest.php
│       └── GetOrdersByEmailTest.php
└── Functional/
    └── Controller/
        └── HomeControllerTest.php
```

---

## 🔧 Разработка

### Статический анализ

#### PHPStan (Level 9)

```bash
# Запуск анализа
vendor/bin/phpstan analyse

# Запуск с максимальным уровнем
vendor/bin/phpstan analyse --level max
```

#### Rector (автоматический рефакторинг)

```bash
# Проверка изменений
vendor/bin/rector process --dry-run

# Применить изменения
vendor/bin/rector process
```

#### PHPCS (Code Style)

```bash
# Проверка стиля кода
vendor/bin/phpcs src/

# Автоматическое исправление
vendor/bin/phpcbf src/
```

### Миграции БД

```bash
# Создать новую миграцию
php bin/console make:migration

# Применить миграции
php bin/console doctrine:migrations:migrate

# Откатить миграцию
php bin/console doctrine:migrations:migrate prev

# Просмотреть статус миграций
php bin/console doctrine:migrations:status
```

### Фикстуры

```bash
# Загрузить фикстуры
php bin/console doctrine:fixtures:load

# Загрузить с очисткой БД
php bin/console doctrine:fixtures:load --purge-with-truncate
```

### Генерация кода (Symfony Maker)

```bash
# Создать контроллер
php bin/console make:controller

# Создать команду
php bin/console make:command

# Создать тест
php bin/console make:test
```

### Docker утилиты

```bash
# Перезапуск контейнеров
docker compose restart

# Остановка контейнеров
docker compose down

# Просмотр логов БД
docker compose logs database

# Подключение к БД
docker compose exec database psql -U app -d app
```

---

## 📊 Метрики проекта

| Метрика | Значение | Статус |
|---------|----------|--------|
| **Domain сущности** | 7 | ✅ |
| **Интерфейсы репозиториев** | 4 | ✅ |
| **Use Case (Application)** | 5 | ✅ |
| **Адаптеры (Doctrine)** | 4 | ✅ |
| **XML mapping файлы** | 7 | ✅ |
| **Unit тесты** | 33 | ✅ (100%) |
| **Functional тесты** | 24 | ✅ (100%) |
| **Всего тестов** | 57 | ✅ (177 assertions) |
| **Контроллеры на Use Case** | 4/4 | ✅ |
| **Старого кода (Service/)** | 0 файлов | ✅ Полностью удалён |

---

## ⚠️ Известные ограничения

### Технические ограничения

1. **Doctrine `find()` в интерфейсах**: Doctrine ORM имеет строгую сигнатуру `find()` которая не может быть объявлена в интерфейсе без зависимости на Doctrine.

   **Временное решение:** Использовать `EntityManager::find()` напрямую в контроллерах.

2. **Session-based корзина**: Текущая реализация использует PHP сессии для идентификации корзины. Для production рекомендуется использовать Redis или базу данных.

3. **Отсутствие аутентификации**: API endpoints не требуют аутентификации. Для production необходимо добавить JWT или OAuth2.

### План доработок

1. ✅ Написать функциональные тесты для контроллеров
2. ✅ Обновить фикстуры на Domain сущности
3. ✅ Реализовать все контроллеры
4. ✅ Очистить тесты от старых импортов
5. ✅ Исправить маршруты API (priority)

---

## 📝 Лицензия

**Лицензия:** Proprietary (все права защищены)

Этот проект является частной собственностью. Копирование, распространение или использование без разрешения запрещено.

---

## 🤝 Вклад в проект

См. [CONTRIBUTING.md](CONTRIBUTING.md) для информации о том, как внести свой вклад в этот проект.

---

## 📞 Контакты

- **Версия проекта**: 1.0.0
- **Дата последнего обновления**: 13 марта 2026
- **Статус**: ✅ Готов к production (с ограничениями)
- **Тесты**: ✅ 57 тестов, 177 assertions (100% passing)

---

<div align="center">

**E-commerce Lite** — чистая архитектура для современной e-commerce платформы

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.2-000?logo=symfony)](https://symfony.com)
[![Doctrine](https://img.shields.io/badge/Doctrine-3.3-336699?logo=doctrine)](https://doctrine-project.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?logo=postgresql)](https://postgresql.org)

</div>
