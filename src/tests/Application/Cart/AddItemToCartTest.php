<?php

declare(strict_types=1);

namespace App\Tests\Application\Cart;

use App\Application\Cart\AddItemToCart;
use App\Domain\Entity\Cart;
use App\Domain\Entity\CartItem;
use App\Domain\Entity\Product;
use App\Domain\Port\Repository\CartRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\ProductDoctrineRepository;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: AddItemToCart
 */
class AddItemToCartTest extends TestCase
{
    private CartRepositoryInterface $cartRepo;
    private ProductDoctrineRepository $productRepo;
    private AddItemToCart $useCase;

    protected function setUp(): void
    {
        $this->cartRepo = $this->createMock(CartRepositoryInterface::class);
        $this->productRepo = $this->createMock(ProductDoctrineRepository::class);
        $this->useCase = new AddItemToCart($this->cartRepo, $this->productRepo);
    }

    /**
     * Тест: Успешное добавление товара в корзину
     */
    public function testAddItemToCartSuccessfully(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(100.0);
        $product->setStock(10);
        $product->setActive(true);

        $cart = new Cart();
        $cart->setSessionId('test-session');

        $this->productRepo->method('find')->willReturn($product);
        $this->cartRepo->method('findBySessionId')->willReturn($cart);
        $this->cartRepo->expects($this->once())->method('save')->with($cart);

        // Act
        $cartItem = $this->useCase->handle('test-session', 1, 2);

        // Assert
        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals(2, $cartItem->getQuantity());
        $this->assertEquals(200.0, $cart->getTotalAmount());
        $this->assertEquals(2, $cart->getTotalItems());
    }

    /**
     * Тест: Добавление существующего товара (увеличение количества)
     */
    public function testAddExistingItemIncreasesQuantity(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(100.0);
        $product->setStock(10);
        $product->setActive(true);

        $cart = new Cart();
        $cart->setSessionId('test-session');

        $existingItem = new CartItem();
        $existingItem->setProduct($product);
        $existingItem->setQuantity(1);
        $cart->addItem($existingItem);

        $this->productRepo->method('find')->willReturn($product);
        $this->cartRepo->method('findBySessionId')->willReturn($cart);
        $this->cartRepo->expects($this->once())->method('save')->with($cart);

        // Act
        $cartItem = $this->useCase->handle('test-session', 1, 3);

        // Assert
        $this->assertSame($existingItem, $cartItem);
        $this->assertEquals(4, $cartItem->getQuantity());
        $this->assertEquals(400.0, $cart->getTotalAmount());
    }

    /**
     * Тест: Ошибка — товар не найден
     */
    public function testAddItemWithNonExistentProduct(): void
    {
        // Arrange
        $this->productRepo->method('find')->willReturn(null);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Товар не найден: 999');

        // Act
        $this->useCase->handle('test-session', 999, 1);
    }

    /**
     * Тест: Ошибка — товар не активен
     */
    public function testAddItemWithInactiveProduct(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(100.0);
        $product->setStock(10);
        $product->setActive(false);

        $this->productRepo->method('find')->willReturn($product);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Товар не активен: 1');

        // Act
        $this->useCase->handle('test-session', 1, 1);
    }

    /**
     * Тест: Ошибка — недостаточно товара на складе
     */
    public function testAddItemWithInsufficientStock(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(100.0);
        $product->setStock(2); // Всего 2 штуки
        $product->setActive(true);

        $this->productRepo->method('find')->willReturn($product);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Недостаточно товара на складе: 1');

        // Act
        $this->useCase->handle('test-session', 1, 5); // Пытаемся добавить 5
    }
}
