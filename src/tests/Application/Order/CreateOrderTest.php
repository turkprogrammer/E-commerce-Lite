<?php

declare(strict_types=1);

namespace App\Tests\Application\Order;

use App\Application\Order\CreateOrder;
use App\Domain\Port\Repository\OrderRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: CreateOrder
 */
class CreateOrderTest extends TestCase
{
    private OrderRepositoryInterface $orderRepo;
    private CreateOrder $useCase;

    protected function setUp(): void
    {
        $this->orderRepo = $this->createMock(OrderRepositoryInterface::class);
        $this->useCase = new CreateOrder($this->orderRepo);
    }

    /**
     * Тест: Успешное создание заказа
     */
    public function testCreateOrderSuccessfully(): void
    {
        // Arrange
        $data = [
            'customerName' => 'Иван Иванов',
            'customerEmail' => 'ivan@example.com',
            'customerPhone' => '+7 (999) 123-45-67',
            'deliveryAddress' => 'г. Москва, ул. Пушкина, д. 10',
            'notes' => 'Позвонить за 30 минут',
            'shippingCost' => 500.0,
            'discountAmount' => 0.0,
        ];

        $items = [
            [
                'productName' => 'Товар 1',
                'quantity' => 2,
                'price' => 100.0,
            ],
            [
                'productName' => 'Товар 2',
                'quantity' => 1,
                'price' => 200.0,
            ],
        ];

        $this->orderRepo->expects($this->once())->method('save');

        // Act
        $order = $this->useCase->handle($data, $items);

        // Assert
        $this->assertEquals('Иван Иванов', $order->getCustomerName());
        $this->assertEquals('ivan@example.com', $order->getCustomerEmail());
        $this->assertCount(2, $order->getItems());
        $this->assertStringStartsWith('ORD-', $order->getOrderNumber());
    }

    /**
     * Тест: Создание заказа без элементов
     */
    public function testCreateOrderWithoutItems(): void
    {
        // Arrange
        $data = [
            'customerName' => 'Иван Иванов',
            'customerEmail' => 'ivan@example.com',
            'customerPhone' => '+7 (999) 123-45-67',
            'deliveryAddress' => 'г. Москва',
        ];

        $items = [];

        $this->orderRepo->expects($this->once())->method('save');

        // Act
        $order = $this->useCase->handle($data, $items);

        // Assert
        $this->assertCount(0, $order->getItems());
        $this->assertEquals(0.0, $order->getTotalAmount());
    }
}
