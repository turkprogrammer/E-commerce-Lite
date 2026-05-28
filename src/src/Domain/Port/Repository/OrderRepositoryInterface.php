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
     *
     * @return Order|null
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
     *
     * @param Order $order Заказ для сохранения
     */
    public function save(Order $order): void;

    /**
     * Удалить заказ
     *
     * @param Order $order Заказ для удаления
     */
    public function delete(Order $order): void;
}
