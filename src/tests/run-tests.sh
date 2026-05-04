#!/bin/bash
# Скрипт запуска тестов
# Использование: ./tests/run-tests.sh [опции phpunit]
#
# Перед запуском рекомендуется выполнить:
#   ./tests/sync-db.sh  # Синхронизировать test БД с dev

set -e

# Находим контейнер shop-php-fpm
CONTAINER_ID=$(docker ps --filter "name=shop-php-fpm" --format "{{.ID}}" | head -1)

if [ -z "$CONTAINER_ID" ]; then
    echo "❌ Контейнер shop-php-fpm не найден!"
    exit 1
fi

echo "🐳 Контейнер: $CONTAINER_ID"
echo ""

# Запускаем тесты
echo "🧪 Запуск тестов..."
docker exec $CONTAINER_ID php /application/bin/phpunit "$@"

echo ""
echo "📊 Готово!"
