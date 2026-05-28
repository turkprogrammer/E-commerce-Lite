<?php

declare(strict_types=1);

namespace App\Tests\Application\Payment;

use App\Application\Payment\ProcessPaymentWebhook;
use App\Domain\Entity\Payment;
use App\Domain\Port\Repository\PaymentRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для Use Case: ProcessPaymentWebhook
 */
class ProcessPaymentWebhookTest extends TestCase
{
    private PaymentRepositoryInterface $paymentRepo;
    private ProcessPaymentWebhook $useCase;

    protected function setUp(): void
    {
        $this->paymentRepo = $this->createMock(PaymentRepositoryInterface::class);
        $this->useCase = new ProcessPaymentWebhook(
            $this->paymentRepo
        );
    }

    /**
     * Тест: Успешная обработка webhook о платеже
     */
    public function testHandlePaymentSucceeded(): void
    {
        // Arrange
        $payload = [
            'type' => 'payment.succeeded',
            'payment_number' => 'PAY-TEST-123',
            'amount' => 1000.00,
            'method' => 'card',
        ];

        $payment = new Payment();
        $payment->setPaymentNumber('PAY-TEST-123');
        $payment->setAmount(1000.00);
        $payment->setMethod('card');
        $payment->setStatus('paid');

        $this->paymentRepo
            ->method('findByPaymentNumber')
            ->with('PAY-TEST-123')
            ->willReturn(null);

        $this->paymentRepo
            ->expects($this->once())
            ->method('save');

        // Act
        $result = $this->useCase->handle($payload);

        // Assert
        $this->assertEquals('paid', $result->getStatus());
        $this->assertEquals('PAY-TEST-123', $result->getPaymentNumber());
        $this->assertEquals(1000.00, $result->getAmount());
    }

    /**
     * Тест: Обработка webhook о неудачном платеже
     */
    public function testHandlePaymentFailed(): void
    {
        // Arrange
        $payload = [
            'type' => 'payment.failed',
            'payment_number' => 'PAY-TEST-456',
            'amount' => 500.00,
            'reason' => 'Insufficient funds',
        ];

        $payment = new Payment();
        $payment->setPaymentNumber('PAY-TEST-456');
        $payment->setAmount(500.00);
        $payment->setMethod('mock');
        $payment->setStatus('failed');

        $this->paymentRepo
            ->method('findByPaymentNumber')
            ->with('PAY-TEST-456')
            ->willReturn(null);

        $this->paymentRepo
            ->expects($this->once())
            ->method('save');

        // Act
        $result = $this->useCase->handle($payload);

        // Assert
        $this->assertEquals('failed', $result->getStatus());
    }

    /**
     * Тест: Обработка webhook о возврате платежа (refund)
     */
    public function testHandlePaymentRefunded(): void
    {
        // Arrange
        $payload = [
            'type' => 'payment.refunded',
            'payment_number' => 'PAY-TEST-789',
            'refund_amount' => 1000.00,
        ];

        $existingPayment = new Payment();
        $existingPayment->setPaymentNumber('PAY-TEST-789');
        $existingPayment->setAmount(1000.00);
        $existingPayment->setStatus('paid');

        $this->paymentRepo
            ->method('findByPaymentNumber')
            ->with('PAY-TEST-789')
            ->willReturn($existingPayment);

        $this->paymentRepo
            ->expects($this->once())
            ->method('save')
            ->with($existingPayment);

        // Act
        $result = $this->useCase->handle($payload);

        // Assert
        $this->assertEquals('refunded', $result->getStatus());
        $this->assertSame($existingPayment, $result);
    }

    /**
     * Тест: Неизвестный тип webhook
     */
    public function testHandleUnknownWebhookType(): void
    {
        // Arrange
        $payload = [
            'type' => 'payment.unknown',
            'payment_number' => 'PAY-TEST-999',
        ];

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Неизвестный тип webhook: payment.unknown');

        // Act
        $this->useCase->handle($payload);
    }

    /**
     * Тест: Обновление существующего платежа
     */
    public function testHandleUpdateExistingPayment(): void
    {
        // Arrange
        $payload = [
            'type' => 'payment.succeeded',
            'payment_number' => 'PAY-EXISTING',
            'amount' => 2000.00,
            'method' => 'bank_transfer',
        ];

        $existingPayment = new Payment();
        $existingPayment->setPaymentNumber('PAY-EXISTING');
        $existingPayment->setAmount(2000.00);
        $existingPayment->setMethod('bank_transfer');
        $existingPayment->setStatus('paid');

        $this->paymentRepo
            ->method('findByPaymentNumber')
            ->with('PAY-EXISTING')
            ->willReturn($existingPayment);

        $this->paymentRepo
            ->expects($this->once())
            ->method('save');

        // Act
        $result = $this->useCase->handle($payload);

        // Assert
        $this->assertEquals('paid', $result->getStatus());
        $this->assertEquals('bank_transfer', $result->getMethod());
    }
}
