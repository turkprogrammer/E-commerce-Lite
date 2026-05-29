<?php

declare(strict_types=1);

namespace App\Tests\Application\Cart;

use App\Application\Cart\CheckoutCart;
use App\Application\Cart\CheckoutData;
use App\Domain\Entity\Cart;
use App\Domain\Entity\CartItem;
use App\Domain\Entity\Product;
use App\Domain\Port\Repository\CartRepositoryInterface;
use App\Domain\Exception\CartEmptyException;
use App\Domain\Exception\CartNotFoundException;
use App\Domain\Port\Repository\OrderRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: CheckoutCart
 */
class CheckoutCartTest extends TestCase
{
    private CartRepositoryInterface $cartRepo;
    private OrderRepositoryInterface $orderRepo;
    private CheckoutCart $useCase;

    protected function setUp(): void
    {
        $this->cartRepo = $this->createMock(CartRepositoryInterface::class);
        $this->orderRepo = $this->createMock(OrderRepositoryInterface::class);
        $this->useCase = new CheckoutCart($this->cartRepo, $this->orderRepo);
    }

    /**
     * Тест: Успешное оформление заказа
     */
    public function testCheckoutCartSuccessfully(): void
    {
        // Arrange
        $product = new Product();
        $product->setId(1);
        $product->setName('Test Product');
        $product->setPrice(1000.00);
        $product->setStock(10);
        $product->setActive(true);

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(2);

        $cart = new Cart();
        $cart->setSessionId('test-session');
        $cart->addItem($cartItem);
        $cart->recalculate();

        $checkoutData = new CheckoutData(
            customerName: 'Иван Иванов',
            customerEmail: 'ivan@example.com',
            customerPhone: '+7 (999) 123-45-67',
            deliveryAddress: 'г. Москва, ул. Тестовая, д. 1',
        );

        $this->cartRepo
            ->method('findBySessionId')
            ->with('test-session')
            ->willReturn($cart);

        $this->orderRepo
            ->expects($this->once())
            ->method('save');

        $this->cartRepo
            ->expects($this->once())
            ->method('save')
            ->with($cart);

        // Act
        $order = $this->useCase->handle('test-session', $checkoutData);

        // Assert
        $this->assertNotNull($order->getOrderNumber());
        $this->assertEquals('Иван Иванов', $order->getCustomerName());
        $this->assertEquals('ivan@example.com', $order->getCustomerEmail());
        $this->assertEquals(2000.00, $order->getTotalAmount());
        $this->assertCount(1, $order->getItems());
    }

    /**
     * Тест: Оформление пустой корзины
     */
    public function testCheckoutEmptyCart(): void
    {
        // Arrange
        $cart = new Cart();
        $cart->setSessionId('test-session');

        $this->cartRepo
            ->method('findBySessionId')
            ->with('test-session')
            ->willReturn($cart);

        // Assert
        $this->expectException(CartEmptyException::class);
        $this->expectExceptionMessage('Корзина пуста для сессии: test-session');

        // Act
        $this->useCase->handle('test-session', new CheckoutData('', '', '', ''));
    }

    /**
     * Тест: Оформление несуществующей корзины
     */
    public function testCheckoutNonExistentCart(): void
    {
        // Arrange
        $this->cartRepo
            ->method('findBySessionId')
            ->with('non-existent')
            ->willReturn(null);

        // Assert
        $this->expectException(CartNotFoundException::class);
        $this->expectExceptionMessage('Корзина не найдена для сессии: non-existent');

        // Act
        $this->useCase->handle('non-existent', new CheckoutData('', '', '', ''));
    }

    /**
     * Тест: Товар не активен
     */
    public function testCheckoutWithInactiveProduct(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Inactive Product');
        $product->setPrice(1000.00);
        $product->setStock(10);
        $product->setActive(false);

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(1);

        $cart = new Cart();
        $cart->setSessionId('test-session');
        $cart->addItem($cartItem);

        $this->cartRepo
            ->method('findBySessionId')
            ->with('test-session')
            ->willReturn($cart);

        // Assert
        $this->expectException(\App\Domain\Exception\ProductNotActiveException::class);

        // Act
        $this->useCase->handle('test-session', new CheckoutData('', '', '', ''));
    }

    /**
     * Тест: Недостаточно товара на складе
     */
    public function testCheckoutWithInsufficientStock(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Limited Product');
        $product->setPrice(1000.00);
        $product->setStock(1);  // Только 1 товар
        $product->setActive(true);

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(5);  // Пытаемся купить 5

        $cart = new Cart();
        $cart->setSessionId('test-session');
        $cart->addItem($cartItem);

        $this->cartRepo
            ->method('findBySessionId')
            ->with('test-session')
            ->willReturn($cart);

        // Assert
        $this->expectException(\App\Domain\Exception\InsufficientStockException::class);

        // Act
        $this->useCase->handle('test-session', new CheckoutData('', '', '', ''));
    }

    /**
     * Тест: Корзина очищается после оформления
     */
    public function testCartClearedAfterCheckout(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(1000.00);
        $product->setStock(10);
        $product->setActive(true);

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(1);

        $cart = new Cart();
        $cart->setSessionId('test-session');
        $cart->addItem($cartItem);
        $cart->recalculate();

        $this->cartRepo
            ->method('findBySessionId')
            ->willReturn($cart);

        // Act
        $this->useCase->handle('test-session', new CheckoutData(
            customerName: 'Test',
            customerEmail: 'test@test.com',
            customerPhone: '+7 (999) 000-00-00',
            deliveryAddress: 'Test Address',
        ));

        // Assert
        $this->assertTrue($cart->isEmpty());
    }
}
