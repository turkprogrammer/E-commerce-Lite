<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Фикстуры для пользователей
 */
class UserFixtures extends Fixture
{
    public const string ADMIN_USER = 'admin-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Загрузить фикстуры пользователей
     */
    public function load(ObjectManager $manager): void
    {
        // Создаем администратора
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        
        // Хешируем пароль
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $this->addReference(self::ADMIN_USER, $admin);

        // Создаем тестового пользователя
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'user123');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
