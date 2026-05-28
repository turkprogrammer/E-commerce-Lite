<?php

declare(strict_types=1);

namespace App\Domain\Port\Repository;

use App\Domain\Entity\Product;

/**
 * Порт для репозитория товаров
 */
interface ProductRepositoryInterface
{
    /**
     * Найти товар по ID
     */
    public function findById(int $id): ?Product;

    /**
     * Найти все товары
     *
     * @return Product[]
     */
    public function findAll(): array;

    /**
     * Найти избранные товары
     *
     * @param int $limit Максимальное количество
     * @return Product[]
     */
    public function findFeatured(int $limit = 10): array;
}
