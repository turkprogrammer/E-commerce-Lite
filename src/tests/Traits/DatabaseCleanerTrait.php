<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Трейт для очистки БД после каждого теста
 */
trait DatabaseCleanerTrait
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Очищаем БД перед тестом
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        // Очищаем БД после теста
        $this->cleanDatabase();
        
        parent::tearDown();
    }

    /**
     * Очистить все таблицы БД
     */
    private function cleanDatabase(): void
    {
        if (!$this instanceof \Symfony\Bundle\FrameworkBundle\Test\WebTestCase) {
            return;
        }

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $connection = $entityManager->getConnection();

        // Отключаем foreign key checks
        $connection->executeStatement('PRAGMA foreign_keys = OFF');

        // Получаем список таблиц
        $tables = $connection->fetchAllAssociative("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'doctrine_migration_versions'");

        // Очищаем каждую таблицу
        foreach ($tables as $table) {
            $connection->executeStatement('DELETE FROM ' . $table['name']);
        }

        // Включаем foreign key checks обратно
        $connection->executeStatement('PRAGMA foreign_keys = ON');
    }
}
