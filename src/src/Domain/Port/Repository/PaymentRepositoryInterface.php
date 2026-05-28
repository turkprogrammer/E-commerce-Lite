<?php

declare(strict_types=1);

namespace App\Domain\Port\Repository;

use App\Domain\Entity\Payment;

/**
 * Порт для репозитория платежей
 */
interface PaymentRepositoryInterface
{
    /**
     * Найти платёж по ID
     *
     * @return Payment|null
     */
    public function findById(int $id): ?Payment;

    /**
     * Найти платёж по номеру
     *
     * @return Payment|null
     */
    public function findByPaymentNumber(string $paymentNumber): ?Payment;

    /**
     * Найти платежи по заказу
     *
     * @return Payment[]
     */
    public function findByOrderId(int $orderId): array;

    /**
     * Сохранить платёж
     *
     * @param Payment $payment Платёж для сохранения
     */
    public function save(Payment $payment): void;

    /**
     * Найти все платежи
     *
     * @return Payment[]
     */
    public function findAll(): array;
}
