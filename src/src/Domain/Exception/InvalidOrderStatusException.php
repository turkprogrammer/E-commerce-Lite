<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Недопустимый статус заказа
 */
class InvalidOrderStatusException extends DomainException
{
    public function __construct(string $status)
    {
        parent::__construct(sprintf('Недопустимый статус заказа: "%s". Допустимые: %s', $status, implode(', ', \App\Domain\Entity\Order::STATUSES)));
    }
}
