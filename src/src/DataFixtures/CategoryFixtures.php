<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Фикстуры для категорий
 */
class CategoryFixtures extends Fixture
{
    public const CATEGORY_ELECTRONICS = 'category_electronics';
    public const CATEGORY_CLOTHING = 'category_clothing';
    public const CATEGORY_HOME = 'category_home';
    public const CATEGORY_BOOKS = 'category_books';

    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => 'Электроника',
                'slug' => 'electronics',
                'reference' => self::CATEGORY_ELECTRONICS,
            ],
            [
                'name' => 'Одежда',
                'slug' => 'clothing',
                'reference' => self::CATEGORY_CLOTHING,
            ],
            [
                'name' => 'Дом и сад',
                'slug' => 'home-garden',
                'reference' => self::CATEGORY_HOME,
            ],
            [
                'name' => 'Книги',
                'slug' => 'books',
                'reference' => self::CATEGORY_BOOKS,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setActive(true);

            $manager->persist($category);
            $this->addReference($categoryData['reference'], $category);
        }

        $manager->flush();
    }
}
