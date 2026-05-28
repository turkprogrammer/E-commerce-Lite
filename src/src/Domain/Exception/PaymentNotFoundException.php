<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Платёж не найден
 */
class PaymentNotFoundException extends DomainException
{
    public function __construct(string $paymentNumber)
    {
        parent::__construct(sprintf('Платёж не найден: %s', $paymentNumber));
    }
}
