<?php

declare(strict_types=1);

namespace App\Tests\Application\Cart;

use App\Application\Cart\RemoveItemFromCart;
use App\Domain\Entity\Cart;
use App\Domain\Entity\CartItem;
use App\Domain\Entity\Product;
use App\Domain\Port\Repository\CartRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: RemoveItemFromCart
 */
class RemoveItemFromCartTest extends TestCase
{
    private CartRepositoryInterface $cartRepo;
    private RemoveItemFromCart $useCase;

    protected function setUp(): void
    {
        $this->cartRepo = $this->createMock(CartRepositoryInterface::class);
        $this->useCase = new RemoveItemFromCart($this->cartRepo);
    }

    /**
     * Тест: Успешное удаление товара из корзины
     */
    public function testRemoveItemSuccessfully(): void
    {
        // Arrange
        $product = new Product();
        $product->setId(1);
        $product->setName('Test Product');

        $cart = new Cart();
        $cart->setSessionId('test-session');

        $item = new CartItem();
        $item->setProduct($product);
        $item->setQuantity(2);
        $cart->addItem($item);

        $this->cartRepo->method('findBySessionId')->willReturn($cart);
        $this->cartRepo->expects($this->once())->method('save')->with($cart);

        // Act
        $this->useCase->handle('test-session', 1);

        // Assert
        $this->assertCount(0, $cart->getItems());
        $this->assertEquals(0.0, $cart->getTotalAmount());
        $this->assertEquals(0, $cart->getTotalItems());
    }

    /**
     * Тест: Ошибка — корзина не найдена
     */
    public function testRemoveItemWithNonExistentCart(): void
    {
        // Arrange
        $this->cartRepo->method('findBySessionId')->willReturn(null);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Корзина не найдена');

        // Act
        $this->useCase->handle('non-existent-session', 1);
    }

    /**
     * Тест: Ошибка — товар не найден в корзине
     */
    public function testRemoveItemWithNonExistentProduct(): void
    {
        // Arrange
        $product = new Product();
        $product->setId(1);

        $cart = new Cart();
        $cart->setSessionId('test-session');

        $item = new CartItem();
        $item->setProduct($product);
        $cart->addItem($item);

        $this->cartRepo->method('findBySessionId')->willReturn($cart);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Товар не найден в корзине');

        // Act
        $this->useCase->handle('test-session', 999);
    }
}
