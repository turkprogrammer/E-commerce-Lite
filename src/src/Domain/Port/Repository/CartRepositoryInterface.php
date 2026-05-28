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
     *
     * @return Cart|null
     */
    public function findBySessionId(string $sessionId): ?Cart;

    /**
     * Сохранить корзину
     *
     * @param Cart $cart Корзина для сохранения
     */
    public function save(Cart $cart): void;

    /**
     * Удалить корзину
     *
     * @param Cart $cart Корзина для удаления
     */
    public function delete(Cart $cart): void;
}
