<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Domain\Entity\Product;
use App\Domain\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Traits\DatabaseCleanerTrait;

/**
 * Функциональные тесты для Product API
 */
class ProductControllerTest extends WebTestCase
{
    use DatabaseCleanerTrait;
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    /**
     * @return array<mixed>
     */
    private function decodeResponse(): array
    {
        $content = $this->client->getResponse()->getContent();
        assert($content !== false);
        $data = json_decode($content, true);
        assert(is_array($data));
        return $data;
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $em;
    }

    /**
     * Тест: Получение товаров с категориями
     */
    public function testGetProductsWithCategories(): void
    {
        // Arrange
        $category = new Category();
        $category->setName('Электроника');
        $category->setActive(true);
        $this->entityManager->persist($category);

        $product = new Product();
        $product->setName('Смартфон');
        $product->setPrice(50000.00);
        $product->setStock(10);
        $product->setActive(true);
        $product->setCategory($category);
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        // Act
        $this->client->request('GET', '/api/products');

        // Assert
        $this->assertResponseIsSuccessful();
        
        $data = $this->decodeResponse();
        $this->assertFalse($data['error']);
        $this->assertArrayHasKey('products', $data['data']);
        $this->assertNotEmpty($data['data']['products']);
        
        // Проверяем, что категория возвращается
        $firstProduct = $data['data']['products'][0];
        $this->assertArrayHasKey('category', $firstProduct);
        $this->assertArrayHasKey('name', $firstProduct['category']);
        $this->assertEquals('Электроника', $firstProduct['category']['name']);
    }

    /**
     * Тест: Получение товара по ID
     */
    public function testGetProductById(): void
    {
        // Arrange
        $category = new Category();
        $category->setName('Test Category');
        $category->setActive(true);
        $this->entityManager->persist($category);

        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(1000.00);
        $product->setStock(10);
        $product->setActive(true);
        $product->setCategory($category);
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $productId = $product->getId();

        // Act
        $this->client->request('GET', sprintf('/api/products/%d', $productId));

        // Assert
        $this->assertResponseIsSuccessful();
        
        $data = $this->decodeResponse();
        $this->assertFalse($data['error']);
        $this->assertArrayHasKey('product', $data['data']);
        $this->assertEquals('Test Product', $data['data']['product']['name']);
    }

    /**
     * Тест: Получение несуществующего товара
     */
    public function testGetNonExistentProduct(): void
    {
        // Act
        $this->client->request('GET', '/api/products/99999');

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Тест: Получение неактивного товара
     */
    public function testGetInactiveProduct(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Inactive Product');
        $product->setPrice(1000.00);
        $product->setStock(10);
        $product->setActive(false);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $productId = $product->getId();

        // Act
        $this->client->request('GET', sprintf('/api/products/%d', $productId));

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Тест: Получение избранных товаров
     */
    public function testGetFeaturedProducts(): void
    {
        // Arrange
        $category = new Category();
        $category->setName('Featured Category');
        $category->setActive(true);
        $this->entityManager->persist($category);

        for ($i = 0; $i < 5; $i++) {
            $product = new Product();
            $product->setName("Featured Product {$i}");
            $product->setPrice(1000.00 * ($i + 1));
            $product->setStock(10);
            $product->setActive(true);
            $product->setCategory($category);
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        // Act
        $this->client->request('GET', '/api/products/featured?limit=3');

        // Assert
        $this->assertResponseIsSuccessful();
        
        $data = $this->decodeResponse();
        $this->assertFalse($data['error']);
        $this->assertArrayHasKey('products', $data['data']);
        $this->assertLessThanOrEqual(3, count($data['data']['products']));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Очистка БД после тестов
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM categories');
    }
}
