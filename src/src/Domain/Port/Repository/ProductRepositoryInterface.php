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
     * Найти все товары
     *
     * @return Product[]
     */
    public function findAll(): array;
}
