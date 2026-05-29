<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Entity\OrderStatus;
use App\Domain\Entity\Payment;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для сущности Order
 */
class OrderTest extends TestCase
{
    private Order $order;

    protected function setUp(): void
    {
        $this->order = new Order();
    }

    /**
     * Тест: createdAt устанавливается при создании
     */
    public function testCreatedAtIsSetOnConstruction(): void
    {
        // Arrange & Act
        $order = new Order();

        // Assert
        $this->assertInstanceOf(\DateTimeImmutable::class, $order->getCreatedAt());
    }

    /**
     * Тест: updatedAt устанавливается при создании
     */
    public function testUpdatedAtIsSetOnConstruction(): void
    {
        // Arrange & Act
        $order = new Order();

        // Assert
        $this->assertInstanceOf(\DateTimeImmutable::class, $order->getUpdatedAt());
    }

    /**
     * Тест: preUpdate обновляет updatedAt
     */
    public function testUpdatedAtIsUpdatedOnPreUpdate(): void
    {
        // Arrange
        $order = new Order();
        $order->setCustomerName('Test User');
        $order->setCustomerEmail('test@example.com');
        $order->setCustomerPhone('+123');
        $order->setDeliveryAddress('Test Address');
        
        $beforeUpdate = $order->getUpdatedAt();
        sleep(1); // Ждём секунду для уверенности

        // Act
        $order->preUpdate();

        // Assert
        $this->assertGreaterThan($beforeUpdate, $order->getUpdatedAt());
    }

    /**
     * Тест: Добавление элемента в заказ
     */
    public function testAddItem(): void
    {
        // Arrange
        $orderItem = new OrderItem();
        $orderItem->setProductName('Test Product');
        $orderItem->setQuantity(2);
        $orderItem->setPrice(1000.00);

        // Act
        $this->order->addItem($orderItem);

        // Assert
        $this->assertCount(1, $this->order->getItems());
        $this->assertSame($this->order, $orderItem->getOrder());
    }

    /**
     * Тест: Удаление элемента из заказа
     */
    public function testRemoveItem(): void
    {
        // Arrange
        $orderItem = new OrderItem();
        $orderItem->setProductName('Test Product');
        $orderItem->setQuantity(2);
        $orderItem->setPrice(1000.00);
        
        $this->order->addItem($orderItem);

        // Act
        $this->order->removeItem($orderItem);

        // Assert
        $this->assertCount(0, $this->order->getItems());
    }

    /**
     * Тест: Пересчет суммы заказа
     */
    public function testRecalculate(): void
    {
        // Arrange
        $item1 = new OrderItem();
        $item1->setProductName('Product 1');
        $item1->setQuantity(2);
        $item1->setPrice(1000.00);

        $item2 = new OrderItem();
        $item2->setProductName('Product 2');
        $item2->setQuantity(1);
        $item2->setPrice(500.00);

        $this->order->addItem($item1);
        $this->order->addItem($item2);

        // Act
        $this->order->recalculate();

        // Assert
        $this->assertEquals(2500.00, $this->order->getTotalAmount()); // (2*1000) + (1*500)
    }

    /**
     * Тест: Добавление платежа
     */
    public function testAddPayment(): void
    {
        // Arrange
        $payment = new Payment();
        // Payment entity needs proper setup based on its structure

        // Act
        $this->order->addPayment($payment);

        // Assert
        $this->assertCount(1, $this->order->getPayments());
        $this->assertSame($this->order, $payment->getOrder());
    }

    /**
     * Тест: Удаление платежа
     */
    public function testRemovePayment(): void
    {
        // Arrange
        $payment = new Payment();
        $this->order->addPayment($payment);

        // Act
        $this->order->removePayment($payment);

        // Assert
        $this->assertCount(0, $this->order->getPayments());
    }

    /**
     * Тест: isPaid возвращает false без платежей
     */
    public function testIsPaidReturnsFalseWithoutPayments(): void
    {
        // Assert
        $this->assertFalse($this->order->isPaid());
    }

    /**
     * Тест: getLastPayment возвращает null без платежей
     */
    public function testGetLastPaymentReturnsNullWithoutPayments(): void
    {
        // Assert
        $this->assertNull($this->order->getLastPayment());
    }

    /**
     * Тест: Установка и получение customerName
     */
    public function testSetAndGetCustomerName(): void
    {
        // Act
        $this->order->setCustomerName('Иван Иванов');

        // Assert
        $this->assertEquals('Иван Иванов', $this->order->getCustomerName());
    }

    /**
     * Тест: Установка и получение customerEmail
     */
    public function testSetAndGetCustomerEmail(): void
    {
        // Act
        $this->order->setCustomerEmail('ivan@example.com');

        // Assert
        $this->assertEquals('ivan@example.com', $this->order->getCustomerEmail());
    }

    /**
     * Тест: Установка и получение status
     */
    public function testSetAndGetStatus(): void
    {
        // Act
        $this->order->setStatus(OrderStatus::Confirmed);

        // Assert
        $this->assertSame(OrderStatus::Confirmed, $this->order->getStatus());
    }

    /**
     * Тест: Номер заказа генерируется автоматически
     */
    public function testOrderNumberIsGeneratedAutomatically(): void
    {
        // Arrange & Act
        $order = new Order();

        // Assert
        $this->assertStringStartsWith('ORD-', $order->getOrderNumber());
    }
}
