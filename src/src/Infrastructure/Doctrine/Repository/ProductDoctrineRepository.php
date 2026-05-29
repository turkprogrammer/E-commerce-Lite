<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Product;
use App\Domain\Port\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine адаптер для ProductRepositoryInterface
 *
 * @extends ServiceEntityRepository<Product>
 */
class ProductDoctrineRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findById(int $id): ?Product
    {
        return $this->find($id);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function findFeatured(int $limit = 10): array
    {
        /** @var Product[] $result */
        $result = $this->createQueryBuilder('p')
            ->where('p.active = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
