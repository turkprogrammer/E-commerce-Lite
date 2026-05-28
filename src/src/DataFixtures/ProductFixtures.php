<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Фикстуры для товаров
 */
class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Загрузить фикстуры товаров
     */
    public function load(ObjectManager $manager): void
    {
        $products = [
            // Электроника
            [
                'name' => 'Смартфон XYZ Pro',
                'description' => 'Современный смартфон с отличной камерой и быстрым процессором',
                'sku' => 'ELEC-001',
                'price' => 49990.00,
                'stock' => 50,
                'category' => CategoryFixtures::CATEGORY_ELECTRONICS,
            ],
            [
                'name' => 'Ноутбук UltraBook 15',
                'description' => 'Легкий и мощный ноутбук для работы и развлечений',
                'sku' => 'ELEC-002',
                'price' => 89990.00,
                'stock' => 25,
                'category' => CategoryFixtures::CATEGORY_ELECTRONICS,
            ],
            [
                'name' => 'Планшет TabMax 10',
                'description' => 'Планшет с большим экраном и длительным временем работы',
                'sku' => 'ELEC-003',
                'price' => 29990.00,
                'stock' => 40,
                'category' => CategoryFixtures::CATEGORY_ELECTRONICS,
            ],
            [
                'name' => 'Беспроводные наушники SoundPro',
                'description' => 'Наушники с активным шумоподавлением',
                'sku' => 'ELEC-004',
                'price' => 12990.00,
                'stock' => 100,
                'category' => CategoryFixtures::CATEGORY_ELECTRONICS,
            ],

            // Одежда
            [
                'name' => 'Футболка Classic White',
                'description' => 'Базовая белая футболка из 100% хлопка',
                'sku' => 'CLTH-001',
                'price' => 1490.00,
                'stock' => 200,
                'category' => CategoryFixtures::CATEGORY_CLOTHING,
            ],
            [
                'name' => 'Джинсы Slim Fit',
                'description' => 'Узкие джинсы классического кроя',
                'sku' => 'CLTH-002',
                'price' => 4990.00,
                'stock' => 80,
                'category' => CategoryFixtures::CATEGORY_CLOTHING,
            ],
            [
                'name' => 'Куртка зимняя Arctic',
                'description' => 'Теплая зимняя куртка с капюшоном',
                'sku' => 'CLTH-003',
                'price' => 12990.00,
                'stock' => 30,
                'category' => CategoryFixtures::CATEGORY_CLOTHING,
            ],

            // Дом и сад
            [
                'name' => 'Набор постельного белья Premium',
                'description' => 'Комплект из 100% сатина, евро размер',
                'sku' => 'HOME-001',
                'price' => 5990.00,
                'stock' => 60,
                'category' => CategoryFixtures::CATEGORY_HOME,
            ],
            [
                'name' => 'Торшер Modern Light',
                'description' => 'Стильный напольный светильник',
                'sku' => 'HOME-002',
                'price' => 8990.00,
                'stock' => 20,
                'category' => CategoryFixtures::CATEGORY_HOME,
            ],
            [
                'name' => 'Набор кухонных ножей Chef',
                'description' => 'Профессиональный набор из 5 ножей',
                'sku' => 'HOME-003',
                'price' => 7490.00,
                'stock' => 45,
                'category' => CategoryFixtures::CATEGORY_HOME,
            ],

            // Книги
            [
                'name' => 'Чистый код. Создание, анализ, рефакторинг',
                'description' => 'Роберт Мартин - классика программирования',
                'sku' => 'BOOK-001',
                'price' => 1200.00,
                'stock' => 100,
                'category' => CategoryFixtures::CATEGORY_BOOKS,
            ],
            [
                'name' => 'Совершенный код',
                'description' => 'Стив Макконнелл - настольная книга разработчика',
                'sku' => 'BOOK-002',
                'price' => 1800.00,
                'stock' => 75,
                'category' => CategoryFixtures::CATEGORY_BOOKS,
            ],
            [
                'name' => 'Грокаем алгоритмы',
                'description' => 'Адитья Бхаргава - алгоритмы для начинающих',
                'sku' => 'BOOK-003',
                'price' => 990.00,
                'stock' => 120,
                'category' => CategoryFixtures::CATEGORY_BOOKS,
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setPrice($productData['price']);
            $product->setStock($productData['stock']);
            $product->setActive(true);

            // Получаем категорию напрямую через константу
            /** @var Category $categoryRef */
            $categoryRef = $this->getReference($productData['category'], Category::class);
            $product->setCategory($categoryRef);

            $manager->persist($product);
        }

        $manager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
