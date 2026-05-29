<?php

declare(strict_types=1);

namespace App\Application\Cart;

use App\Domain\Entity\Cart;
use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Exception\CartEmptyException;
use App\Domain\Exception\CartNotFoundException;
use App\Domain\Exception\InsufficientStockException;
use App\Domain\Exception\ProductNotActiveException;
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
     * @param CheckoutData $checkoutData Данные покупателя
     * @return Order Созданный заказ
     * @throws CartNotFoundException Если корзина не найдена
     * @throws CartEmptyException Если корзина пуста
     * @throws ProductNotActiveException Если товар не активен
     * @throws InsufficientStockException Если недостаточно товара на складе
     */
    public function handle(string $sessionId, CheckoutData $checkoutData): Order
    {
        // Находим корзину
        $cart = $this->cartRepo->findBySessionId($sessionId);

        if (!$cart) {
            throw new CartNotFoundException($sessionId);
        }

        // Проверяем что корзина не пуста
        if ($cart->isEmpty()) {
            throw new CartEmptyException($sessionId);
        }

        // Проверяем наличие товаров
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();
            assert($product !== null);

            if (!$product->isActive()) {
                throw new ProductNotActiveException($product->getId());
            }

            if ($product->getStock() < $cartItem->getQuantity()) {
                throw new InsufficientStockException(
                    $product->getId(),
                    $cartItem->getQuantity(),
                    $product->getStock()
                );
            }
        }
        
        // Создаём заказ
        $order = new Order();
        $order->setCustomerName($checkoutData->customerName);
        $order->setCustomerEmail($checkoutData->customerEmail);
        $order->setCustomerPhone($checkoutData->customerPhone);
        $order->setDeliveryAddress($checkoutData->deliveryAddress);
        
        // Переносим элементы корзины в заказ
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();
            assert($product !== null);

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
