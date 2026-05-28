<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Функциональные тесты для главной страницы
 *
 * Проверяет корректность отображения и базовую функциональность
 */
class HomeControllerTest extends WebTestCase
{
    /**
     * Тест: Главная страница загружается корректно
     *
     * ПРОВЕРКА: Базовый HTTP-ответ
     */
    public function testHomePageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Тест: Главная страница содержит структуру для загрузки товаров
     *
     * ПРОВЕРКА: Наличие контейнеров и JS для динамической загрузки
     */
    public function testHomePageHasProductLoadingStructure(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $html = $client->getResponse()->getContent();

        // Проверяем наличие контейнера для товаров
        $this->assertSelectorExists('#collection', 'Секция товаров должна существовать');
        $this->assertSelectorExists('#products-container', 'Контейнер для товаров должен существовать');

        // Проверяем наличие JS-функций (инлайн в шаблоне)
        $this->assertStringContainsString('function loadMore', $html, 'Функция loadMore должна быть');
        $this->assertStringContainsString('function createProductCard', $html, 'Функция createProductCard должна быть');
        $this->assertStringContainsString('function addToCart', $html, 'Функция addToCart должна быть');
    }

    /**
     * Тест: Главная страница содержит навигацию и брендинг
     *
     * ПРОВЕРКА: Пользователь видит правильную навигацию
     */
    public function testHomePageHasBrandingAndNavigation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Проверяем навигацию
        $this->assertSelectorExists('nav', 'Навигация должна существовать');

        // Проверяем логотип/название
        $this->assertSelectorTextContains('span', 'ECOMMERCE', 'Название магазина должно быть');

        // Проверяем навигационные ссылки
        $this->assertSelectorExists('a[href="#collection"]', 'Ссылка на каталог должна быть');
        $this->assertSelectorExists('a[href="#about"]', 'Ссылка About должна быть');
        $this->assertSelectorExists('a[href="#contact"]', 'Ссылка Contact должна быть');
    }

    /**
     * Тест: Главная страница содержит заголовок
     *
     * ПРОВЕРКА: Контент соответствует premium-дизайну
     */
    public function testHomePageHasLatestDropsHeader(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Define Your Style', 'Заголовок Define Your Style должен быть');
    }

    /**
     * Тест: Главная страница содержит кнопку корзины
     *
     * ПРОВЕРКА: Функционал корзины доступен
     */
    public function testHomePageHasCartButton(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Проверяем кнопку корзины (SVG иконка + счетчик)
        $this->assertSelectorExists('button[onclick*="toggleCart"]', 'Кнопка корзины должна быть');
        $this->assertSelectorExists('#cart-count', 'Счетчик корзины должен быть');
        $this->assertSelectorExists('#cart-sidebar', 'Сайдбар корзины должен быть');
    }
}
