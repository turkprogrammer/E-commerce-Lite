<?php

declare(strict_types=1);

namespace App\Application\Cart;

use App\Domain\Entity\Cart;
use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Port\Repository\CartRepositoryInterface;
use App\Domain\Port\Repository\OrderRepositoryInterface;

/**
 * Use Case: Оформить заказ из корзины (Checkout)
 */
readonly class CheckoutCart
{
    public function __construct(
        private CartRepositoryInterface $cartRepo,
        private OrderRepositoryInterface $orderRepo,
    ) {}

    /**
     * Оформить заказ из корзины
     *
     * @param string $sessionId Session ID корзины
     * @param array{customerName: string, customerEmail: string, customerPhone: string, deliveryAddress: string} $customerData Данные покупателя
     * @return Order Созданный заказ
     * @throws \RuntimeException Если корзина пуста или не найдена
     */
    public function handle(string $sessionId, array $customerData): Order
    {
        // Находим корзину
        $cart = $this->cartRepo->findBySessionId($sessionId);
        
        if (!$cart) {
            throw new \RuntimeException('Корзина не найдена');
        }
        
        // Проверяем что корзина не пуста
        if ($cart->isEmpty()) {
            throw new \RuntimeException('Корзина пуста');
        }
        
        // Проверяем наличие товаров
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();
            
            if (!$product->isActive()) {
                throw new \RuntimeException(
                    sprintf('Товар "%s" больше не доступен', $product->getName())
                );
            }
            
            if ($product->getStock() < $cartItem->getQuantity()) {
                throw new \RuntimeException(
                    sprintf(
                        'Недостаточно товара "%s" на складе. Доступно: %d',
                        $product->getName(),
                        $product->getStock()
                    )
                );
            }
        }
        
        // Создаём заказ
        $order = new Order();
        $order->setCustomerName($customerData['customerName']);
        $order->setCustomerEmail($customerData['customerEmail']);
        $order->setCustomerPhone($customerData['customerPhone']);
        $order->setDeliveryAddress($customerData['deliveryAddress']);
        
        // Переносим элементы корзины в заказ
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();
            
            $orderItem = new OrderItem();
            $orderItem->setProductName($product->getName());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($product->getPrice());
            
            $order->addItem($orderItem);
        }
        
        // Пересчитываем сумму заказа
        $order->recalculate();
        
        // Уменьшаем склад
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();
            $product->setStock($product->getStock() - $cartItem->getQuantity());
        }
        
        // Сохраняем заказ
        $this->orderRepo->save($order);
        
        // Очищаем корзину
        $cart->clear();
        $this->cartRepo->save($cart);
        
        return $order;
    }
}
