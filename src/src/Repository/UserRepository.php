<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Сохраняет пользователя в БД
     */
    public function save(User $user, bool $andFlush = true): void
    {
        $this->getEntityManager()->persist($user);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Удаляет пользователя из БД
     */
    public function remove(User $user, bool $andFlush = true): void
    {
        $this->getEntityManager()->remove($user);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @see PasswordUpgraderInterface
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf(
                'Instances of "%s" are not supported.',
                $user::class
            ));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user);
    }
}
