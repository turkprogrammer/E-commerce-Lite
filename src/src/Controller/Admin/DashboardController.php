<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\DashboardStatsProvider;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Главная панель администратора
 */
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private DashboardStatsProvider $statsProvider,
    ) {}

    /**
     * Главная страница админки - Dashboard
     */
    public function index(): Response
    {
        $stats = $this->statsProvider->getStats();
        $salesChart = $this->statsProvider->getSalesChart();
        $topProducts = $this->statsProvider->getTopProducts();
        $orderStatuses = $this->statsProvider->getOrderStatuses();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'salesChart' => $salesChart,
            'topProducts' => $topProducts,
            'orderStatuses' => $orderStatuses,
        ]);
    }

    /**
     * Настройка дашборда
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('E-commerce Admin')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    /**
     * Настройка меню
     */
    public function configureMenuItems(): iterable
    {
        // Главная - Dashboard
        yield MenuItem::linkToDashboard('Главная', 'fa fa-home');

        // Раздел каталога
        yield MenuItem::section('Каталог');
        yield MenuItem::linkToRoute('Товары', 'fas fa-box', 'admin_product_index');
        yield MenuItem::linkToRoute('Категории', 'fas fa-tags', 'admin_category_index');

        // Раздел заказов
        yield MenuItem::section('Заказы');
        yield MenuItem::linkToRoute('Заказы', 'fas fa-shopping-cart', 'admin_order_index');

        // Ссылка на сайт
        yield MenuItem::section('');
        yield MenuItem::linkToUrl('На сайт', 'fas fa-external-link-alt', '/');

        // Выход
        yield MenuItem::linkToLogout('Выйти', 'fas fa-sign-out-alt');
    }
}
