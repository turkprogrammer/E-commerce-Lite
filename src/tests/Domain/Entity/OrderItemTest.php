<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\OrderItem;
use App\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для сущности OrderItem
 */
class OrderItemTest extends TestCase
{
    private OrderItem $orderItem;

    protected function setUp(): void
    {
        $this->orderItem = new OrderItem();
    }

    /**
     * Тест: setProduct и getProduct
     */
    public function testSetProductAndGetProduct(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');

        // Act
        $this->orderItem->setProduct($product);

        // Assert
        $this->assertSame($product, $this->orderItem->getProduct());
    }

    /**
     * Тест: __toString возвращает название продукта и количество
     */
    public function testToStringReturnsProductNameAndQuantity(): void
    {
        // Arrange
        $this->orderItem->setProductName('Test Product');
        $this->orderItem->setQuantity(3);

        // Act
        $result = (string) $this->orderItem;

        // Assert
        $this->assertEquals('Test Product (x3)', $result);
    }

    /**
     * Тест: recalculate пересчитывает totalPrice
     */
    public function testRecalculateTotalPrice(): void
    {
        // Arrange
        $this->orderItem->setPrice(1000.00);
        $this->orderItem->setQuantity(5);

        // Act
        // recalculate вызывается автоматически внутри setPrice и setQuantity

        // Assert
        $this->assertEquals(5000.00, $this->orderItem->getTotalPrice());
    }

    /**
     * Тест: setPrice обновляет totalPrice
     */
    public function testSetPriceUpdatesTotalPrice(): void
    {
        // Arrange
        $this->orderItem->setQuantity(2);

        // Act
        $this->orderItem->setPrice(500.00);

        // Assert
        $this->assertEquals(1000.00, $this->orderItem->getTotalPrice());
    }

    /**
     * Тест: setQuantity обновляет totalPrice
     */
    public function testSetQuantityUpdatesTotalPrice(): void
    {
        // Arrange
        $this->orderItem->setPrice(1000.00);

        // Act
        $this->orderItem->setQuantity(3);

        // Assert
        $this->assertEquals(3000.00, $this->orderItem->getTotalPrice());
    }

    /**
     * Тест: Установка и получение productName
     */
    public function testSetAndGetProductName(): void
    {
        // Act
        $this->orderItem->setProductName('Test Product Name');

        // Assert
        $this->assertEquals('Test Product Name', $this->orderItem->getProductName());
    }

    /**
     * Тест: Установка и получение price
     */
    public function testSetAndGetPrice(): void
    {
        // Act
        $this->orderItem->setPrice(1234.56);

        // Assert
        $this->assertEquals(1234.56, $this->orderItem->getPrice());
    }

    /**
     * Тест: Установка и получение quantity
     */
    public function testSetAndGetQuantity(): void
    {
        // Act
        $this->orderItem->setQuantity(10);

        // Assert
        $this->assertEquals(10, $this->orderItem->getQuantity());
    }

    /**
     * Тест: getTotalPrice возвращает правильное значение
     */
    public function testGetTotalPrice(): void
    {
        // Arrange
        $this->orderItem->setPrice(100.00);
        $this->orderItem->setQuantity(5);

        // Assert
        $this->assertEquals(500.00, $this->orderItem->getTotalPrice());
    }

    /**
     * Тест: getId возвращает null для новой сущности
     */
    public function testGetIdReturnsNullForNewEntity(): void
    {
        // Assert
        $this->assertNull($this->orderItem->getId());
    }
}
