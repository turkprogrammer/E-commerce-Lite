<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\DashboardStatsProvider;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
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
     * Загрузка глобальных CSS-ассетов для админки
     */
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin-theme.css');
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
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-gauge-high');

        yield MenuItem::section('Каталог');
        yield MenuItem::linkToRoute('Товары', 'fas fa-cube', 'admin_product_index');
        yield MenuItem::linkToRoute('Категории', 'fas fa-layer-group', 'admin_category_index');

        yield MenuItem::section('Продажи');
        yield MenuItem::linkToRoute('Заказы', 'fas fa-receipt', 'admin_order_index');

        yield MenuItem::section('');
        yield MenuItem::linkToUrl('На сайт', 'fas fa-arrow-up-right-from-square', '/')->setLinkTarget('_blank');
        yield MenuItem::linkToLogout('Выход', 'fas fa-right-from-bracket');
    }
}
