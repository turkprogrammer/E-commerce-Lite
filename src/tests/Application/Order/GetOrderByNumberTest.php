<?php

declare(strict_types=1);

namespace App\Tests\Application\Order;

use App\Application\Order\GetOrderByNumber;
use App\Domain\Entity\Order;
use App\Domain\Port\Repository\OrderRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: GetOrderByNumber
 */
class GetOrderByNumberTest extends TestCase
{
    private OrderRepositoryInterface $orderRepo;
    private GetOrderByNumber $useCase;

    protected function setUp(): void
    {
        $this->orderRepo = $this->createMock(OrderRepositoryInterface::class);
        $this->useCase = new GetOrderByNumber($this->orderRepo);
    }

    /**
     * Тест: Успешное получение заказа по номеру
     */
    public function testGetOrderByNumberSuccessfully(): void
    {
        // Arrange
        $order = new Order();
        $orderNumber = 'ORD-TEST-123';

        $this->orderRepo
            ->method('findByOrderNumber')
            ->with($orderNumber)
            ->willReturn($order);

        // Act
        $result = $this->useCase->handle($orderNumber);

        // Assert
        $this->assertSame($order, $result);
    }

    /**
     * Тест: Заказ не найден
     */
    public function testGetOrderByNumberNotFound(): void
    {
        // Arrange
        $orderNumber = 'ORD-NOT-FOUND';

        $this->orderRepo
            ->method('findByOrderNumber')
            ->with($orderNumber)
            ->willReturn(null);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Заказ не найден: $orderNumber");

        // Act
        $this->useCase->handle($orderNumber);
    }
}
