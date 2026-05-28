<?php

declare(strict_types=1);

namespace App\Application\Cart;

use App\Domain\Exception\CartNotFoundException;
use App\Domain\Exception\CartItemNotFoundException;
use App\Domain\Port\Repository\CartRepositoryInterface;

/**
 * Use Case: Удалить товар из корзины
 */
readonly class RemoveItemFromCart
{
    public function __construct(
        private CartRepositoryInterface $cartRepo,
    ) {}

    /**
     * Удалить товар из корзины
     *
     * @param string $sessionId Session ID
     * @param int $productId ID товара
     * @return void
     * @throws CartNotFoundException Если корзина не найдена
     * @throws CartItemNotFoundException Если товар не найден в корзине
     */
    public function handle(string $sessionId, int $productId): void
    {
        $cart = $this->cartRepo->findBySessionId($sessionId);
        if (!$cart) {
            throw new CartNotFoundException($sessionId);
        }

        // Находим товар в корзине и удаляем
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $productId) {
                $cart->removeItem($item);
                $cart->recalculate();
                $this->cartRepo->save($cart);
                return;
            }
        }

        throw new CartItemNotFoundException($productId);
    }
}
