# 🤝 Вклад в проект E-commerce Lite

Благодарим за интерес к проекту! Это руководство поможет вам внести свой вклад в развитие проекта.

---

## 📋 Содержание

- [Начало работы](#начало-работы)
- [Стандарты кода](#стандарты-кода)
- [Процесс разработки](#процесс-разработки)
- [Тестирование](#тестирование)
- [Pull Request](#pull-request)
- [Code Review](#code-review)

---

## 🚀 Начало работы

### 1. Форк репозитория

Создайте форк репозитория на GitHub и склонируйте его:

```bash
git clone git@github.com:your-username/e-commerce-lite.git
cd e-commerce-lite
```

### 2. Настройка окружения

```bash
# Установка зависимостей
composer install

# Копирование .env
cp .env .env.local

# Запуск Docker контейнеров
docker compose up -d

# Создание БД и миграции
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 3. Создание ветки

```bash
# Обновите master ветку
git checkout master
git pull origin master

# Создайте новую ветку для фичи/багфикса
git checkout -b feature/your-feature-name
# или
git checkout -b fix/issue-123
```

**Naming convention для веток:**

| Префикс | Описание | Пример |
|---------|----------|--------|
| `feature/` | Новая функциональность | `feature/cart-discount` |
| `fix/` | Исправление бага | `fix/cart-calculation` |
| `refactor/` | Рефакторинг кода | `refactor/order-service` |
| `docs/` | Документация | `docs/api-reference` |
| `test/` | Тесты | `test/cart-unit-tests` |
| `chore/` | Вспомогательные изменения | `chore/update-deps` |

---

## 📝 Стандарты кода

### PHP Code Style

Проект следует стандартам **PSR-12**:

```bash
# Проверка стиля кода
vendor/bin/phpcs src/

# Автоматическое исправление
vendor/bin/phpcbf src/
```

### Статический анализ

```bash
# PHPStan (Level 9)
vendor/bin/phpstan analyse

# Rector (автоматический рефакторинг)
vendor/bin/rector process --dry-run
vendor/bin/rector process
```

### Требования к коду

| Требование | Описание |
|------------|----------|
| **Типизация** | Все параметры и возвращаемые значения типизированы |
| **Strict types** | `declare(strict_types=1);` в начале каждого файла |
| **Имена переменных** | camelCase, понятные имена (`$productName`, не `$p`) |
| **Имена классов** | PascalCase, существительные (`ProductRepository`) |
| **Имена методов** | camelCase, глаголы (`findProductById`) |
| **Константы** | UPPER_SNAKE_CASE (`MAX_CART_ITEMS`) |
| **Комментарии** | Только на русском, объясняют **почему**, а не **что** |

### Пример правильного кода

```php
<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Товар
 */
class Product
{
    public function __construct(
        private string $name,
        private float $price,
        private int $stock = 0,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }
}
```

### Запрещено

- ❌ Глобальные переменные
- ❌ Singletons (используйте DI)
- ❌ Игнорирование ошибок (пустые catch блоки)
- ❌ Монолитные функции >50 строк
- ❌ Хардкод конфигурации (используйте Env/Config)
- ❌ Аннотации Doctrine в Domain сущностях (только XML mapping)

---

## 🔄 Процесс разработки

### TDD Workflow (Red → Green → Refactor)

1. **Напишите тест** (красный)
2. **Запустите тест** (убедитесь, что падает)
3. **Напишите код** (минимально для прохождения)
4. **Запустите тест** (зелёный)
5. **Рефакторинг** (код + тесты)

### Пример

```php
// 1. Тест (tests/Application/Cart/AddItemToCartTest.php)
public function testAddItemToCart(): void
{
    $cart = new Cart();
    $product = new Product('Test', 1000, 10);
    
    $useCase = new AddItemToCart($cartRepo, $productRepo);
    $item = $useCase->handle('session123', 1, 2);
    
    self::assertEquals(2, $item->getQuantity());
}

// 2. Запуск теста (падает)
vendor/bin/phpunit tests/Application/Cart/AddItemToCartTest.php

// 3. Реализация
class AddItemToCart
{
    public function handle(string $sessionId, int $productId, int $quantity): CartItem
    {
        // ...
    }
}

// 4. Запуск теста (проходит)

// 5. Рефакторинг
```

---

## 🧪 Тестирование

### Запуск тестов

```bash
# Все тесты (рекомендуется)
./tests/run-tests.sh

# Все тесты с подробным выводом
./tests/run-tests.sh --testdox

# Application слой (Unit тесты)
./tests/run-tests.sh --testsuite Application

# Functional тесты
./tests/run-tests.sh --testsuite Functional

# Конкретный файл
./tests/run-tests.sh tests/Application/Cart/AddItemToCartTest.php

# Конкретный тест
./tests/run-tests.sh --filter testAddItemToCart

# Напрямую через Docker
docker exec shop-php-fpm-1 php /application/bin/phpunit --testdox
```

### Синхронизация БД

Перед запуском тестов рекомендуется синхронизировать test БД с dev:

```bash
./tests/sync-db.sh
```

### Статистика тестов

| Категория | Всего | Пройдено | Assertions |
|-----------|-------|----------|------------|
| **Unit тесты** (Application) | 33 | ✅ 100% | 118 |
| **Functional тесты** | 24 | ✅ 100% | 59 |
| **Всего** | **57** | ✅ **100%** | **177** |

### Требования к тестам

| Требование | Описание |
|------------|----------|
| **Покрытие** | Минимум 80% для Application слоя |
| **Изоляция** | Тесты не зависят друг от друга |
| **Моки** | Используйте моки для внешних зависимостей |
| **Имена** | `test<Method><Scenario><ExpectedResult>` |
| **Arrange-Act-Assert** | Структура теста |

### Пример хорошего теста

```php
public function testAddItemToCart_WhenProductNotActive_ThrowsRuntimeException(): void
{
    // Arrange
    $product = new Product('Test', 1000, 10);
    $product->setActive(false);
    
    $productRepo = $this->createMock(ProductRepositoryInterface::class);
    $productRepo->method('find')->willReturn($product);
    
    $useCase = new AddItemToCart($this->cartRepo, $productRepo);
    
    // Act & Assert
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Товар не активен');
    
    $useCase->handle('session123', 1, 2);
}
```

---

## 📬 Pull Request

### Чек-лист перед отправкой

- [ ] Код следует стандартам проекта (PSR-12)
- [ ] Все тесты проходят (`vendor/bin/phpunit`)
- [ ] PHPStan без ошибок (`vendor/bin/phpstan analyse`)
- [ ] Rector без замечаний (`vendor/bin/rector process --dry-run`)
- [ ] Добавлены тесты для новой функциональности
- [ ] Обновлена документация (README, CHANGELOG)
- [ ] Commit message следует конвенции

### Структура PR

**Заголовок:**

```
<type>(<scope>): <description>

# Примеры:
feat(cart): добавить скидку на корзину
fix(order): исправление расчёта общей суммы
docs(readme): обновить API документацию
```

**Описание PR:**

```markdown
## 🎯 Цель
Краткое описание того, что делает этот PR.

## 📋 Изменения
- [ ] Добавлен Use Case ApplyDiscount
- [ ] Обновлён CartController
- [ ] Добавлены тесты

## 🧪 Тестирование
- Unit тесты: `vendor/bin/phpunit tests/Application/Cart`
- Functional тесты: `vendor/bin/phpunit tests/Functional/Controller/CartControllerTest.php`

## ⚠️ Breaking Changes
- [ ] Нет
- [ ] Да (описать)

## 📸 Скриншоты (если применимо)

## 🔗 Связанные задачи
Closes #123
```

---

## 🔍 Code Review

### Критерии ревью

| Критерий | Вопросы |
|----------|---------|
| **Архитектура** | Следует ли код гексагональной архитектуре? |
| **Тесты** | Покрыты ли тестами новые функции? |
| **Производительность** | Нет ли N+1 запросов? |
| **Безопасность** | Проверены ли входные данные? |
| **Читаемость** | Понятен ли код без комментариев? |
| **Сложность** | Можно ли упростить? |

### Обратная связь

**Хорошо:**

> ✅ Отличная работа! Use Case хорошо изолирован, тесты покрывают граничные случаи.

**Требует улучшений:**

> ⚠️ Функция `calculateTotal()` слишком большая (80 строк). Предлагаю выделить методы `calculateSubtotal()`, `calculateTax()`, `applyDiscounts()`.

**Критично:**

> ❌ Нельзя игнорировать ошибки. Оберните вызов API в try-catch и логируйте ошибку.

---

## 📚 Ресурсы

- [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)
- [PSR-12 Coding Style](https://www.php-fig.org/psr/psr-12/)
- [Hexagonal Architecture](https://alistair.cockburn.us/hexagonal-architecture/)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [TDD](https://martinfowler.com/bliki/TestDrivenDevelopment.html)

---

## 🎯 Roadmap

### В работе

- [ ] Реализация `WebhookController` на Use Case
- [ ] Добавление методов `find()` через Query Objects
- [ ] Интеграция с платёжными системами
- [ ] Аутентификация (JWT/OAuth2)

### Планируется

- [ ] GraphQL API
- [ ] Event Sourcing для заказов
- [ ] CQRS для чтения/записи
- [ ] Микросервисная архитектура (опционально)

---

## 📞 Контакты

- **Issues**: [GitHub Issues](https://github.com/your-repo/e-commerce-lite/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-repo/e-commerce-lite/discussions)

---

<div align="center">

**Спасибо за ваш вклад!** 🚀

Каждый PR делает проект лучше для всех.

</div>
