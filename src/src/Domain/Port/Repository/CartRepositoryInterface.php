<?php

declare(strict_types=1);

namespace App\Domain\Port\Repository;

use App\Domain\Entity\Cart;

/**
 * Порт для репозитория корзины
 */
interface CartRepositoryInterface
{
    /**
     * Найти корзину по session ID
     */
    public function findBySessionId(string $sessionId): ?Cart;

    /**
     * Сохранить корзину
     */
    public function save(Cart $cart): void;

    /**
     * Удалить корзину
     */
    public function delete(Cart $cart): void;
}
