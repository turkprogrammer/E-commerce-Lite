<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Entity\Order;
use App\Domain\Port\Repository\OrderRepositoryInterface;

/**
 * Use Case: Получить заказы по email
 */
readonly class GetOrdersByEmail
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
    ) {}

    /**
     * Получить заказы по email
     *
     * @param string $email Email покупателя
     * @return Order[]
     */
    public function handle(string $email): array
    {
        return $this->orderRepo->findByEmail($email);
    }
}
