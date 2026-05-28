<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Port\Repository\OrderRepositoryInterface;

/**
 * Use Case: Создать заказ
 */
readonly class CreateOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
    ) {}

    /**
     * Создать заказ
     *
     * @param array{customerName: string, customerEmail: string, customerPhone: string, deliveryAddress: string} $data Данные заказа
     * @param array<int, array{productName: string, quantity: int, price: float}> $items Элементы заказа
     * @return Order Созданный заказ
     */
    public function handle(array $data, array $items): Order
    {
        $order = new Order();
        $order->setCustomerName($data['customerName']);
        $order->setCustomerEmail($data['customerEmail']);
        $order->setCustomerPhone($data['customerPhone']);
        $order->setDeliveryAddress($data['deliveryAddress']);

        // Добавляем элементы заказа
        foreach ($items as $itemData) {
            $item = new OrderItem();
            $item->setProductName($itemData['productName']);
            $item->setQuantity($itemData['quantity']);
            $item->setPrice($itemData['price']);
            $order->addItem($item);
        }

        // Пересчитываем общую сумму
        $order->recalculate();

        // Сохраняем
        $this->orderRepo->save($order);

        return $order;
    }
}
