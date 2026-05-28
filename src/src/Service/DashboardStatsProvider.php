<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Сервис статистики для Dashboard
 */
class DashboardStatsProvider
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * Получить основную статистику
     */
    public function getStats(): array
    {
        $connection = $this->entityManager->getConnection();

        // Получаем текущую дату в формате SQLite
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Выручка за сегодня (исключая отменённые)
        $todayRevenue = $connection->fetchOne('
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE DATE(created_at) = :today
            AND status != "cancelled"
        ', ['today' => $today]);

        // Выручка за вчера
        $yesterdayRevenue = $connection->fetchOne('
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE DATE(created_at) = :yesterday
            AND status != "cancelled"
        ', ['yesterday' => $yesterday]);

        // Новые заказы за сегодня
        $todayOrders = $connection->fetchOne('
            SELECT COUNT(*)
            FROM orders
            WHERE DATE(created_at) = :today
        ', ['today' => $today]);

        // Новые заказы за вчера
        $yesterdayOrders = $connection->fetchOne('
            SELECT COUNT(*)
            FROM orders
            WHERE DATE(created_at) = :yesterday
        ', ['yesterday' => $yesterday]);

        // Средний чек за сегодня (только активные заказы)
        $averageCheck = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;

        // Всего заказов (все)
        $totalOrders = $connection->fetchOne('SELECT COUNT(*) FROM orders');

        // Всего активных товаров
        $totalProducts = $connection->fetchOne('SELECT COUNT(*) FROM products WHERE active = 1');

        // Выручка за неделю (только активные заказы)
        $weekRevenue = $connection->fetchOne('
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE DATE(created_at) >= DATE(:today, "-7 days")
            AND status != "cancelled"
        ', ['today' => $today]);

        // Выручка за месяц (только активные заказы)
        $monthRevenue = $connection->fetchOne('
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE DATE(created_at) >= DATE(:today, "-30 days")
            AND status != "cancelled"
        ', ['today' => $today]);

        return [
            'todayRevenue' => (float)$todayRevenue,
            'todayRevenueChange' => $yesterdayRevenue > 0 
                ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1) 
                : 0,
            'todayOrders' => (int)$todayOrders,
            'todayOrdersChange' => $yesterdayOrders > 0 
                ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1) 
                : 0,
            'averageCheck' => (float)$averageCheck,
            'totalOrders' => (int)$totalOrders,
            'totalProducts' => (int)$totalProducts,
            'weekRevenue' => (float)$weekRevenue,
            'monthRevenue' => (float)$monthRevenue,
        ];
    }

    /**
     * Получить динамику продаж по дням (последние 7 дней)
     */
    public function getSalesChart(): array
    {
        $connection = $this->entityManager->getConnection();
        
        $today = date('Y-m-d');

        $rows = $connection->fetchAllAssociative('
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as orders,
                COALESCE(SUM(total_amount), 0) as revenue
            FROM orders
            WHERE DATE(created_at) >= DATE(:today, "-7 days")
            AND status != "cancelled"
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ', ['today' => $today]);

        // Заполняем пропущенные дни
        $result = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $found = false;
            
            foreach ($rows as $row) {
                if ($row['date'] === $date) {
                    $result[] = [
                        'date' => date('d.m', strtotime($date)),
                        'orders' => (int)$row['orders'],
                        'revenue' => (float)$row['revenue'],
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $result[] = [
                    'date' => date('d.m', strtotime($date)),
                    'orders' => 0,
                    'revenue' => 0,
                ];
            }
        }

        return $result;
    }

    /**
     * Получить топ-5 самых продаваемых товаров
     */
    public function getTopProducts(int $limit = 5): array
    {
        $connection = $this->entityManager->getConnection();

        $rows = $connection->fetchAllAssociative('
            SELECT
                oi.product_name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status != "cancelled"
            GROUP BY oi.product_name
            ORDER BY total_quantity DESC
            LIMIT :limit
        ', ['limit' => $limit]);

        return array_map(fn($row) => [
            'name' => $row['product_name'],
            'quantity' => (int)$row['total_quantity'],
            'revenue' => (float)$row['total_revenue'],
        ], $rows);
    }

    /**
     * Получить статусы заказов для графика
     */
    public function getOrderStatuses(): array
    {
        $connection = $this->entityManager->getConnection();

        $rows = $connection->fetchAllAssociative('
            SELECT status, COUNT(*) as count
            FROM orders
            GROUP BY status
        ');

        return array_map(fn($row) => [
            'status' => $row['status'],
            'count' => (int)$row['count'],
        ], $rows);
    }
}
