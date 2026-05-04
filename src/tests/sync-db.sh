#!/bin/bash
# Синхронизация тестовой БД с dev
set -e

echo "🔄 Синхронизация тестовой БД с dev..."

CONTAINER_ID=$(docker ps --filter "name=shop-php-fpm" --format "{{.ID}}" | head -1)

if [ -z "$CONTAINER_ID" ]; then
    echo "❌ Контейнер не найден!"
    exit 1
fi

echo "🐳 Контейнер: $CONTAINER_ID"

# Создаём фикстуры в dev БД
echo "📦 Загрузка фикстур в dev БД..."
docker exec $CONTAINER_ID php /application/bin/console doctrine:fixtures:load --env=dev --no-interaction -q

# Копируем dev БД в test
echo "📋 Копирование dev БД в test..."
docker exec $CONTAINER_ID rm -f /application/var/data.test.db
docker exec $CONTAINER_ID cp /application/var/data.db /application/var/data.test.db

echo "✅ Готово!"
echo ""
echo "📊 Размер БД:"
docker exec $CONTAINER_ID ls -lh /application/var/*.db
