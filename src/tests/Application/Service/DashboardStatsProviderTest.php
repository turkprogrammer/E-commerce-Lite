<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Service\DashboardStatsProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для сервиса статистики Dashboard
 */
class DashboardStatsProviderTest extends TestCase
{
    private Connection $connection;
    private EntityManagerInterface $entityManager;
    private DashboardStatsProvider $statsProvider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager
            ->method('getConnection')
            ->willReturn($this->connection);
        
        $this->statsProvider = new DashboardStatsProvider($this->entityManager);
    }

    /**
     * Тест: Получение основной статистики
     */
    public function testGetStatsReturnsCorrectData(): void
    {
        // Arrange
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $callCount = 0;

        $this->connection
            ->method('fetchOne')
            ->willReturnCallback(function($sql, $params = []) use (&$callCount) {
                $callCount++;
                
                // 1-й вызов: today revenue
                if ($callCount === 1 && strpos($sql, ':today') !== false && strpos($sql, 'SUM') !== false) {
                    return 50000;
                }
                // 2-й вызов: yesterday revenue
                if ($callCount === 2 && strpos($sql, ':yesterday') !== false && strpos($sql, 'SUM') !== false) {
                    return 40000;
                }
                // 3-й вызов: today orders
                if ($callCount === 3 && strpos($sql, ':today') !== false && strpos($sql, 'COUNT') !== false) {
                    return 5;
                }
                // 4-й вызов: yesterday orders
                if ($callCount === 4 && strpos($sql, ':yesterday') !== false && strpos($sql, 'COUNT') !== false) {
                    return 4;
                }
                // 5-й вызов: total orders
                if ($callCount === 5 && $sql === 'SELECT COUNT(*) FROM orders') {
                    return 100;
                }
                // 6-й вызов: total products
                if ($callCount === 6 && $sql === 'SELECT COUNT(*) FROM products WHERE active = 1') {
                    return 50;
                }
                // 7-й вызов: week revenue
                if ($callCount === 7 && strpos($sql, '-7 days') !== false) {
                    return 200000;
                }
                // 8-й вызов: month revenue
                if ($callCount === 8 && strpos($sql, '-30 days') !== false) {
                    return 800000;
                }
                return 0;
            });

        // Act
        $stats = $this->statsProvider->getStats();

        // Assert
        $this->assertEquals(50000.0, $stats['todayRevenue']);
        $this->assertEquals(25.0, $stats['todayRevenueChange']);
        $this->assertEquals(5, $stats['todayOrders']);
        $this->assertEquals(25.0, $stats['todayOrdersChange']);
        $this->assertEquals(10000.0, $stats['averageCheck']);
        $this->assertEquals(100, $stats['totalOrders']);
        $this->assertEquals(50, $stats['totalProducts']);
        $this->assertEquals(200000.0, $stats['weekRevenue']);
        $this->assertEquals(800000.0, $stats['monthRevenue']);
    }

    /**
     * Тест: Статистика с нулевыми значениями
     */
    public function testGetStatsWithZeroValues(): void
    {
        // Arrange
        $this->connection
            ->method('fetchOne')
            ->willReturn(0);

        // Act
        $stats = $this->statsProvider->getStats();

        // Assert
        $this->assertEquals(0.0, $stats['todayRevenue']);
        $this->assertEquals(0, $stats['todayRevenueChange']);
        $this->assertEquals(0, $stats['todayOrders']);
        $this->assertEquals(0, $stats['todayOrdersChange']);
        $this->assertEquals(0.0, $stats['averageCheck']);
    }

    /**
     * Тест: График продаж за 7 дней
     */
    public function testGetSalesChartReturnsSevenDays(): void
    {
        // Arrange
        $today = date('Y-m-d');
        
        $this->connection
            ->method('fetchAllAssociative')
            ->with($this->stringContains('SELECT'), ['today' => $today])
            ->willReturn([
                ['date' => $today, 'orders' => 5, 'revenue' => 50000],
                ['date' => date('Y-m-d', strtotime('-1 day')), 'orders' => 3, 'revenue' => 30000],
            ]);

        // Act
        $chart = $this->statsProvider->getSalesChart();

        // Assert
        $this->assertCount(7, $chart);
        $this->assertEquals(date('d.m'), $chart[6]['date']); // Сегодняшний день
        $this->assertEquals(5, $chart[6]['orders']);
        $this->assertEquals(50000.0, $chart[6]['revenue']);
    }

    /**
     * Тест: График заполняет пропущенные дни
     */
    public function testGetSalesChartFillsMissingDays(): void
    {
        // Arrange
        $today = date('Y-m-d');

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->willReturn([
                ['date' => $today, 'orders' => '5', 'revenue' => '50000'],
            ]);

        // Act
        $chart = $this->statsProvider->getSalesChart();

        // Assert
        $this->assertCount(7, $chart);

        // Проверяем, что пропущенные дни заполнены нулями
        $zeroDays = array_filter($chart, fn($day) => $day['orders'] === 0 && $day['revenue'] === 0.0);
        $this->assertCount(6, $zeroDays);
    }

    /**
     * Тест: Топ-5 товаров
     */
    public function testGetTopProductsReturnsLimit(): void
    {
        // Arrange
        $this->connection
            ->method('fetchAllAssociative')
            ->willReturn([
                ['product_name' => 'Товар 1', 'total_quantity' => 100, 'total_revenue' => 100000],
                ['product_name' => 'Товар 2', 'total_quantity' => 80, 'total_revenue' => 80000],
                ['product_name' => 'Товар 3', 'total_quantity' => 60, 'total_revenue' => 60000],
                ['product_name' => 'Товар 4', 'total_quantity' => 40, 'total_revenue' => 40000],
                ['product_name' => 'Товар 5', 'total_quantity' => 20, 'total_revenue' => 20000],
            ]);

        // Act
        $topProducts = $this->statsProvider->getTopProducts();

        // Assert
        $this->assertCount(5, $topProducts);
        $this->assertEquals('Товар 1', $topProducts[0]['name']);
        $this->assertEquals(100, $topProducts[0]['quantity']);
        $this->assertEquals(100000.0, $topProducts[0]['revenue']);
    }

    /**
     * Тест: Топ товаров с меньшим количеством
     */
    public function testGetTopProductsWithLessThanFive(): void
    {
        // Arrange
        $this->connection
            ->method('fetchAllAssociative')
            ->willReturn([
                ['product_name' => 'Товар 1', 'total_quantity' => 100, 'total_revenue' => 100000],
                ['product_name' => 'Товар 2', 'total_quantity' => 80, 'total_revenue' => 80000],
            ]);

        // Act
        $topProducts = $this->statsProvider->getTopProducts(5);

        // Assert
        $this->assertCount(2, $topProducts);
    }

    /**
     * Тест: Пустой список товаров
     */
    public function testGetTopProductsWithNoSales(): void
    {
        // Arrange
        $this->connection
            ->method('fetchAllAssociative')
            ->willReturn([]);

        // Act
        $topProducts = $this->statsProvider->getTopProducts();

        // Assert
        $this->assertCount(0, $topProducts);
    }

    /**
     * Тест: Статусы заказов
     */
    public function testGetOrderStatusesGroupsByStatus(): void
    {
        // Arrange
        $this->connection
            ->method('fetchAllAssociative')
            ->willReturn([
                ['status' => 'pending', 'count' => 10],
                ['status' => 'paid', 'count' => 5],
                ['status' => 'delivered', 'count' => 15],
                ['status' => 'cancelled', 'count' => 2],
            ]);

        // Act
        $statuses = $this->statsProvider->getOrderStatuses();

        // Assert
        $this->assertCount(4, $statuses);
        $this->assertEquals('pending', $statuses[0]['status']);
        $this->assertEquals(10, $statuses[0]['count']);
    }
}
