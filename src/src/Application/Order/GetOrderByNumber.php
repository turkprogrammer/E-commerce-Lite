<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Entity\Order;
use App\Domain\Port\Repository\OrderRepositoryInterface;

/**
 * Use Case: Получить заказ по номеру
 */
readonly class GetOrderByNumber
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
    ) {}

    /**
     * Получить заказ по номеру
     *
     * @param string $orderNumber Номер заказа
     * @return Order Найденный заказ
     * @throws \RuntimeException Если заказ не найден
     */
    public function handle(string $orderNumber): Order
    {
        $order = $this->orderRepo->findByOrderNumber($orderNumber);
        
        if (!$order) {
            throw new \RuntimeException("Заказ не найден: $orderNumber");
        }
        
        return $order;
    }
}
