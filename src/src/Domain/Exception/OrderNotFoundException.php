<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Заказ не найден
 */
class OrderNotFoundException extends DomainException
{
    public function __construct(string $orderNumber)
    {
        parent::__construct(sprintf('Заказ не найден: %s', $orderNumber));
    }
}
