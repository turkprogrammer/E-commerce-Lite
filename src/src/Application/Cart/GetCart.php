<?php

declare(strict_types=1);

namespace App\Application\Cart;

use App\Domain\Entity\Cart;
use App\Domain\Port\Repository\CartRepositoryInterface;

/**
 * Use Case: Получить корзину по session ID
 */
readonly class GetCart
{
    public function __construct(
        private CartRepositoryInterface $cartRepo,
    ) {}

    /**
     * Получить корзину
     */
    public function handle(string $sessionId): Cart
    {
        $cart = $this->cartRepo->findBySessionId($sessionId);
        if (!$cart) {
            // Создаём новую корзину если не найдена
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $this->cartRepo->save($cart);
        }

        return $cart;
    }
}
