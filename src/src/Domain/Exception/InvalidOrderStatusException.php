<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\Entity\OrderStatus;

/**
 * Недопустимый статус заказа
 */
class InvalidOrderStatusException extends DomainException
{
    public function __construct(string $status)
    {
        $valid = array_map(fn (OrderStatus $s) => $s->value, OrderStatus::cases());
        parent::__construct(sprintf('Недопустимый статус заказа: "%s". Допустимые: %s', $status, implode(', ', $valid)));
    }
}
