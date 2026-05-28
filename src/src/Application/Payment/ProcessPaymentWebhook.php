<?php

declare(strict_types=1);

namespace App\Application\Payment;

use App\Domain\Entity\Payment;
use App\Domain\Port\Repository\PaymentRepositoryInterface;

/**
 * Use Case: Обработать webhook от платёжной системы
 */
readonly class ProcessPaymentWebhook
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepo,
    ) {}

    /**
     * Обработать webhook
     *
     * @param array<string, mixed> $payload Данные webhook
     * @return Payment Обработанный платёж
     * @throws \RuntimeException Если webhook невалиден
     */
    public function handle(array $payload): Payment
    {
        // Проверяем тип события
        $eventType = $payload['type'] ?? '';
        
        if ($eventType === 'payment.succeeded' || $eventType === 'payment.completed') {
            return $this->handlePaymentSucceeded($payload);
        }
        
        if ($eventType === 'payment.failed') {
            return $this->handlePaymentFailed($payload);
        }
        
        if ($eventType === 'payment.refunded') {
            return $this->handlePaymentRefunded($payload);
        }
        
        throw new \RuntimeException("Неизвестный тип webhook: $eventType");
    }

    /**
     * Об успешном платеже
     *
     * @param array<string, mixed> $payload
     */
    private function handlePaymentSucceeded(array $payload): Payment
    {
        $paymentNumber = $payload['payment_number'] ?? $payload['id'] ?? '';
        
        // Ищем существующий платёж или создаём новый
        $payment = $this->paymentRepo->findByPaymentNumber($paymentNumber);
        
        if (!$payment) {
            // Создаём новый платёж из данных webhook
            $payment = new Payment();
            $payment->setPaymentNumber($paymentNumber);
            $payment->setAmount((float) ($payload['amount'] ?? 0));
            $payment->setMethod($payload['method'] ?? 'unknown');
        }
        
        // Обновляем статус
        $payment->setStatus('paid');
        
        // Сохраняем метаданные
        $payment->setMetadata($payload);
        
        // Сохраняем
        $this->paymentRepo->save($payment);
        
        return $payment;
    }

    /**
     * О неудачном платеже
     *
     * @param array<string, mixed> $payload
     */
    private function handlePaymentFailed(array $payload): Payment
    {
        $paymentNumber = $payload['payment_number'] ?? $payload['id'] ?? '';
        
        $payment = $this->paymentRepo->findByPaymentNumber($paymentNumber);
        
        if (!$payment) {
            $payment = new Payment();
            $payment->setPaymentNumber($paymentNumber);
            $payment->setAmount((float) ($payload['amount'] ?? 0));
        }
        
        $payment->setStatus('failed');
        $payment->setMetadata($payload);
        
        $this->paymentRepo->save($payment);
        
        return $payment;
    }

    /**
     * О возврате платежа (refund)
     *
     * @param array<string, mixed> $payload
     */
    private function handlePaymentRefunded(array $payload): Payment
    {
        $paymentNumber = $payload['payment_number'] ?? $payload['original_payment_id'] ?? '';
        
        $payment = $this->paymentRepo->findByPaymentNumber($paymentNumber);
        
        if (!$payment) {
            throw new \RuntimeException("Платёж не найден: $paymentNumber");
        }
        
        $payment->setStatus('refunded');
        $payment->setMetadata(array_merge($payment->getMetadata(), $payload));
        
        $this->paymentRepo->save($payment);
        
        return $payment;
    }
}
