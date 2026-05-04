<?php

declare(strict_types=1);

namespace App\Tests\Application\Cart;

use App\Application\Cart\GetCart;
use App\Domain\Entity\Cart;
use App\Domain\Port\Repository\CartRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: GetCart
 */
class GetCartTest extends TestCase
{
    private CartRepositoryInterface $cartRepo;
    private GetCart $useCase;

    protected function setUp(): void
    {
        $this->cartRepo = $this->createMock(CartRepositoryInterface::class);
        $this->useCase = new GetCart($this->cartRepo);
    }

    /**
     * Тест: Успешное получение корзины
     */
    public function testGetCartSuccessfully(): void
    {
        // Arrange
        $cart = new Cart();
        $cart->setSessionId('test-session');

        $this->cartRepo->method('findBySessionId')->willReturn($cart);

        // Act
        $result = $this->useCase->handle('test-session');

        // Assert
        $this->assertSame($cart, $result);
        $this->assertEquals('test-session', $result->getSessionId());
    }

    /**
     * Тест: Создание новой корзины если не найдена
     */
    public function testGetCartCreatesNewIfNotFound(): void
    {
        // Arrange
        $this->cartRepo->method('findBySessionId')->willReturn(null);
        $this->cartRepo->expects($this->once())->method('save');

        // Act
        $result = $this->useCase->handle('new-session');

        // Assert
        $this->assertInstanceOf(Cart::class, $result);
        $this->assertEquals('new-session', $result->getSessionId());
    }
}
