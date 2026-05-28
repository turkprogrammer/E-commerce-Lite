<?php

declare(strict_types=1);

namespace App\Application\Cart;

use App\Domain\Entity\Cart;
use App\Domain\Entity\CartItem;
use App\Domain\Exception\InsufficientStockException;
use App\Domain\Exception\ProductNotActiveException;
use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Port\Repository\CartRepositoryInterface;
use App\Domain\Port\Repository\ProductRepositoryInterface;

/**
 * Use Case: Добавить товар в корзину
 */
readonly class AddItemToCart
{
    public function __construct(
        private CartRepositoryInterface $cartRepo,
        private ProductRepositoryInterface $productRepo,
    ) {}

    /**
     * Добавить товар в корзину
     *
     * @param string $sessionId Session ID
     * @param int $productId ID товара
     * @param int $quantity Количество
     * @return CartItem Созданный элемент корзины
     */
    public function handle(string $sessionId, int $productId, int $quantity): CartItem
    {
        // Находим корзину по session ID
        $cart = $this->cartRepo->findBySessionId($sessionId);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
        }

        // Находим товар
        $product = $this->productRepo->findById($productId);
        if (!$product) {
            throw new ProductNotFoundException($productId);
        }

        if (!$product->isActive()) {
            throw new ProductNotActiveException($productId);
        }

        if ($product->getStock() < $quantity) {
            throw new InsufficientStockException($productId, $quantity, $product->getStock());
        }

        // Проверяем валидацию количества с учётом текущих товаров в корзине
        $existingItem = null;
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct() === $product) {
                $existingItem = $item;
                break;
            }
        }

        $currentQuantity = $existingItem?->getQuantity() ?? 0;
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $product->getStock()) {
            throw new InsufficientStockException($productId, $newQuantity, $product->getStock());
        }

        // Если товар уже в корзине, увеличиваем количество
        if ($existingItem) {
            $existingItem->setQuantity($newQuantity);
            $cartItem = $existingItem;
        } else {
            // Создаём новый элемент
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cart->addItem($cartItem);
        }

        // Пересчитываем и сохраняем
        $cart->recalculate();
        $this->cartRepo->save($cart);

        return $cartItem;
    }
}
