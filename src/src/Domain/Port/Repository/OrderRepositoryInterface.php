<?php

declare(strict_types=1);

namespace App\Domain\Port\Repository;

use App\Domain\Entity\Order;

/**
 * Порт для репозитория заказов
 */
interface OrderRepositoryInterface
{
    /**
     * Найти заказ по номеру заказа
     */
    public function findByOrderNumber(string $orderNumber): ?Order;

    /**
     * Найти заказы по email покупателя
     *
     * @return Order[]
     */
    public function findByEmail(string $email): array;

    /**
     * Сохранить заказ
     */
    public function save(Order $order): void;

    /**
     * Удалить заказ
     */
    public function delete(Order $order): void;
}
