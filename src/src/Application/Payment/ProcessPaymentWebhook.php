<?php

declare(strict_types=1);

namespace App\Application\Payment;

use App\Domain\Entity\Payment;
use App\Domain\Exception\PaymentNotFoundException;
use App\Domain\Exception\WebhookException;
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
     * @throws WebhookException Если тип webhook неизвестен
     * @throws PaymentNotFoundException Если платёж не найден при возврате
     */
    public function handle(array $payload): Payment
    {
        // Проверяем тип события
        /** @var string $eventType */
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

        throw WebhookException::unknownType($eventType);
    }

    /**
     * Об успешном платеже
     *
     * @param array<string, mixed> $payload
     */
    private function handlePaymentSucceeded(array $payload): Payment
    {
        /** @var string $paymentNumber */
        $paymentNumber = $payload['payment_number'] ?? $payload['id'] ?? '';

        // Ищем существующий платёж или создаём новый
        $payment = $this->paymentRepo->findByPaymentNumber($paymentNumber);

        if (!$payment) {
            // Создаём новый платёж из данных webhook
            /** @var float $amount */
            $amount = $payload['amount'] ?? 0;
            /** @var string $method */
            $method = $payload['method'] ?? 'unknown';

            $payment = new Payment();
            $payment->setPaymentNumber($paymentNumber);
            $payment->setAmount($amount);
            $payment->setMethod($method);
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
        /** @var string $paymentNumber */
        $paymentNumber = $payload['payment_number'] ?? $payload['id'] ?? '';

        $payment = $this->paymentRepo->findByPaymentNumber($paymentNumber);

        if (!$payment) {
            /** @var float $amount */
            $amount = $payload['amount'] ?? 0;

            $payment = new Payment();
            $payment->setPaymentNumber($paymentNumber);
            $payment->setAmount($amount);
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
        /** @var string $paymentNumber */
        $paymentNumber = $payload['payment_number'] ?? $payload['original_payment_id'] ?? '';

        $payment = $this->paymentRepo->findByPaymentNumber($paymentNumber);
        
        if (!$payment) {
            throw new PaymentNotFoundException($paymentNumber);
        }
        
        $payment->setStatus('refunded');
        $payment->setMetadata(array_merge($payment->getMetadata(), $payload));
        
        $this->paymentRepo->save($payment);
        
        return $payment;
    }
}
