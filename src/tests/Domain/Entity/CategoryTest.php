<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для сущности Category
 */
class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    /**
     * Тест: __toString возвращает название категории
     */
    public function testToStringReturnsName(): void
    {
        // Arrange
        $this->category->setName('Электроника');

        // Act
        $result = (string) $this->category;

        // Assert
        $this->assertEquals('Электроника', $result);
    }

    /**
     * Тест: Установка и получение name
     */
    public function testSetAndGetName(): void
    {
        // Act
        $this->category->setName('Test Category');

        // Assert
        $this->assertEquals('Test Category', $this->category->getName());
    }

    /**
     * Тест: Установка и получение active
     */
    public function testSetAndGetActive(): void
    {
        // Act
        $this->category->setActive(false);

        // Assert
        $this->assertFalse($this->category->isActive());
    }

    /**
     * Тест: active по умолчанию true
     */
    public function testActiveIsTrueByDefault(): void
    {
        // Arrange & Act
        $category = new Category();

        // Assert
        $this->assertTrue($category->isActive());
    }

    /**
     * Тест: Установка и получение parent
     */
    public function testSetAndGetParent(): void
    {
        // Arrange
        $parent = new Category();
        $parent->setName('Parent Category');

        // Act
        $this->category->setParent($parent);

        // Assert
        $this->assertSame($parent, $this->category->getParent());
    }

    /**
     * Тест: Добавление дочерней категории
     */
    public function testAddChild(): void
    {
        // Arrange
        $child = new Category();
        $child->setName('Child Category');

        // Act
        $child->setParent($this->category);

        // Assert
        $this->assertCount(0, $this->category->getChildren());
        $this->assertSame($this->category, $child->getParent());
    }

    /**
     * Тест: getId возвращает null для новой сущности
     */
    public function testGetIdReturnsNullForNewEntity(): void
    {
        // Assert
        $this->assertNull($this->category->getId());
    }

    /**
     * Тест: getChildren возвращает коллекцию
     */
    public function testGetChildrenReturnsCollection(): void
    {
        // Assert
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->category->getChildren());
        $this->assertCount(0, $this->category->getChildren());
    }

    /**
     * Тест: getProducts возвращает коллекцию
     */
    public function testGetProductsReturnsCollection(): void
    {
        // Assert
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->category->getProducts());
        $this->assertCount(0, $this->category->getProducts());
    }

    /**
     * Тест: Добавление продукта в категорию
     */
    public function testAddProduct(): void
    {
        // Arrange
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(1000.00);
        $product->setStock(10);
        $product->setActive(true);

        // Act
        $product->setCategory($this->category);

        // Assert
        $this->assertCount(0, $this->category->getProducts());
        $this->assertSame($this->category, $product->getCategory());
    }
}
