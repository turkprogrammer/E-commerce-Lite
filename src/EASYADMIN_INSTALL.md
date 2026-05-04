# Инструкция по установке EasyAdmin 4

## Проблема
SSL connection timeout при подключении к Packagist.

## Решение

### Когда сеть восстановится:

1. **Установите EasyAdmin через composer:**

```bash
cd D:\docker-projects\symfony\src
composer require admin/easyadmin-bundle --no-interaction
```

2. **Скопируйте vendor в контейнер:**

```bash
docker cp D:/docker-projects/symfony/src/vendor 122a9fc88f78:/application/
```

3. **Очистите кеш:**

```bash
docker exec 122a9fc88f78 php /application/bin/console cache:clear
```

4. **Проверьте доступ:**

Откройте браузер: `http://localhost:47000/admin`

**Логин:** `admin`  
**Пароль:** `admin123`

---

## Альтернативное решение (без composer)

Если сеть не восстановится, можно использовать альтернативный подход:

1. Скачать EasyAdmin вручную с GitHub:
   https://github.com/EasyCorp/EasyAdminBundle/releases

2. Распаковать в `vendor/easycorp/easyadmin-bundle`

3. Зарегистрировать bundle в `config/bundles.php`:

```php
return [
    // ...
    EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle::class => ['all' => true],
];
```

4. Очистить кеш:
```bash
php bin/console cache:clear
```

---

## Созданные файлы (готовы к использованию)

✅ `config/packages/easyadmin.yaml` — конфигурация  
✅ `config/routes/easyadmin.yaml` — маршруты  
✅ `src/Controller/Admin/DashboardController.php` — дашборд  
✅ `src/Controller/Admin/ProductCrudController.php` — товары  
✅ `src/Controller/Admin/CategoryCrudController.php` — категории  
✅ `src/Controller/Admin/OrderCrudController.php` — заказы  
✅ `config/packages/security.yaml` — безопасность  
✅ `.env` — переменные окружения

**Ожидает установки:** EasyAdmin Bundle через composer
