<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Entity\Product;
use App\Domain\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Traits\DatabaseCleanerTrait;

/**
 * Функциональные тесты для Order API
 */
class OrderControllerTest extends WebTestCase
{
    use DatabaseCleanerTrait;
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * Тест: Создание заказа с товарами
     */
    public function testCreateOrderWithItems(): void
    {
        // Arrange
        $category = new Category();
        $category->setName('Test Category');
        $category->setActive(true);
        $this->entityManager->persist($category);

        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(1000.00);
        $product->setStock(100);
        $product->setActive(true);
        $product->setCategory($category);
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $payload = [
            'customerName' => 'Иван Иванов',
            'customerEmail' => 'ivan@example.com',
            'customerPhone' => '+7 (999) 123-45-67',
            'deliveryAddress' => 'г. Москва, ул. Тестовая, д. 1',
            'items' => [
                ['productId' => $product->getId(), 'quantity' => 2],
            ],
        ];

        // Act
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($data['error']);
        $this->assertArrayHasKey('order', $data['data']);
        $this->assertArrayHasKey('orderNumber', $data['data']['order']);
        $this->assertArrayHasKey('totalAmount', $data['data']['order']);
    }

    /**
     * Тест: Создание заказа без товаров
     */
    public function testCreateOrderWithoutItems(): void
    {
        // Arrange
        $payload = [
            'customerName' => 'Иван Иванов',
            'customerEmail' => 'ivan@example.com',
            'customerPhone' => '+7 (999) 123-45-67',
            'deliveryAddress' => 'г. Москва',
            'items' => [],
        ];

        // Act
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        // Assert
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Корзина пуста', $data['message']);
    }

    /**
     * Тест: Создание заказа с невалидными данными
     */
    public function testCreateOrderWithInvalidData(): void
    {
        // Arrange
        $payload = [
            'customerName' => '',
            'customerEmail' => 'invalid-email',
            'items' => [['productId' => 999, 'quantity' => 1]],
        ];

        // Act
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        // Assert
        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * Тест: Получение заказов по email
     */
    public function testGetOrdersByEmail(): void
    {
        // Arrange
        $this->client->request('GET', '/api/orders?email=test@example.com');

        // Assert
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($data['error']);
        $this->assertArrayHasKey('orders', $data['data']);
        $this->assertArrayHasKey('total', $data['data']);
    }

    /**
     * Тест: Получение заказов без email параметра
     */
    public function testGetOrdersWithoutEmail(): void
    {
        // Act
        $this->client->request('GET', '/api/orders');

        // Assert
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['error']);
    }

    /**
     * Тест: Получение заказа по номеру
     */
    public function testGetOrderByNumber(): void
    {
        // Arrange
        $order = new Order();
        $order->setCustomerName('Test User');
        $order->setCustomerEmail('test@example.com');
        $order->setCustomerPhone('+123456789');
        $order->setDeliveryAddress('Test Address');
        $order->setStatus('pending');
        $order->setTotalAmount(1000.00);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $orderNumber = $order->getOrderNumber();

        // Act
        $this->client->request('GET', "/api/orders/{$orderNumber}");

        // Assert
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($data['error']);
        $this->assertArrayHasKey('order', $data['data']);
    }

    /**
     * Тест: Получение несуществующего заказа
     */
    public function testGetNonExistentOrder(): void
    {
        // Act
        $this->client->request('GET', '/api/orders/ORD-NONEXISTENT');

        // Assert
        $this->assertResponseStatusCodeSame(404);
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['error']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Очистка БД после тестов
        $this->entityManager->getConnection()->executeStatement('DELETE FROM order_items');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM orders');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM categories');
    }
}
