<?php

declare(strict_types=1);

namespace App\Domain\Port\Repository;

use App\Domain\Entity\Category;

/**
 * Порт для репозитория категорий
 */
interface CategoryRepositoryInterface
{
    /**
     * Найти активные категории
     *
     * @return Category[]
     */
    public function findActiveCategories(): array;
}
