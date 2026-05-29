<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Gateway;

use App\Domain\Entity\Payment;
use App\Domain\Port\PaymentGatewayInterface;

/**
 * Mock платёжный шлюз для тестов
 */
class MockPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var Payment[]
     */
    private array $payments = [];
    
    /** @var array<int, array<string, mixed>> */
    private array $webhooks = [];
    
    /**
     * Создать платёж
     */
    public function createPayment(float $amount, string $orderId): Payment
    {
        $payment = new Payment();
        $payment->setAmount($amount);
        $payment->setMethod('mock');
        $payment->setStatus('pending');
        
        $this->payments[] = $payment;
        
        return $payment;
    }

    /**
     * Обработать webhook от платёжной системы
     *
     * @param array<string, mixed> $payload
     */
    public function processWebhook(array $payload): Payment
    {
        $this->webhooks[] = $payload;
        
        $payment = new Payment();
        $payment->setPaymentNumber((string) ($payload['payment_number'] ?? 'PAY-MOCK-' . uniqid()));
        $payment->setAmount((float) ($payload['amount'] ?? 0));
        $payment->setMethod((string) ($payload['method'] ?? 'mock'));
        
        // Определяем статус из webhook
        $eventType = $payload['type'] ?? '';
        if ($eventType === 'payment.succeeded' || $eventType === 'payment.completed') {
            $payment->setStatus('paid');
        } elseif ($eventType === 'payment.failed') {
            $payment->setStatus('failed');
        } elseif ($eventType === 'payment.refunded') {
            $payment->setStatus('refunded');
        }
        
        $payment->setMetadata($payload);
        
        return $payment;
    }

    /**
     * Проверить подпись webhook
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        // Для тестов всегда возвращаем true
        return true;
    }

    /**
     * Получить статус платежа
     */
    public function getPaymentStatus(string $paymentId): string
    {
        return 'paid';
    }

    /**
     * Вернуть платёж (refund)
     */
    public function refund(Payment $payment): Payment
    {
        $payment->setStatus('refunded');
        return $payment;
    }
    
    /**
     * Получить все созданные платежи
     *
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }
    
    /**
     * Получить все обработанные webhook
     *
     * @return array<int, array<string, mixed>>
     */
    public function getWebhooks(): array
    {
        return $this->webhooks;
    }
    
    /**
     * Очистить состояние
     */
    public function reset(): void
    {
        $this->payments = [];
        $this->webhooks = [];
    }
}
