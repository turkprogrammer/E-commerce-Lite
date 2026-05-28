<?php

declare(strict_types=1);

namespace App\Domain\Port;

use App\Domain\Entity\Payment;

/**
 * Порт для платёжного шлюза (внешний адаптер)
 */
interface PaymentGatewayInterface
{
    /**
     * Создать платёж
     */
    public function createPayment(float $amount, string $orderId): Payment;

    /**
     * Обработать webhook от платёжной системы
     *
     * @param array<string, mixed> $payload
     */
    public function processWebhook(array $payload): Payment;

    /**
     * Проверить подпись webhook
     */
    public function verifySignature(string $payload, string $signature): bool;

    /**
     * Получить статус платежа
     */
    public function getPaymentStatus(string $paymentId): string;

    /**
     * Вернуть платёж (refund)
     */
    public function refund(Payment $payment): Payment;
}
