<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Payment;
use App\Domain\Port\Repository\PaymentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine адаптер для PaymentRepositoryInterface
 */
class PaymentDoctrineRepository extends ServiceEntityRepository implements PaymentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findById(int $id): ?Payment
    {
        return parent::find($id);
    }

    public function findByPaymentNumber(string $paymentNumber): ?Payment
    {
        return $this->findOneBy(['paymentNumber' => $paymentNumber]);
    }

    public function findByOrderId(int $orderId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.order = :orderId')
            ->setParameter('orderId', $orderId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Payment $payment): void
    {
        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}
