<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Корзина пуста
 */
class CartEmptyException extends DomainException
{
    public function __construct(string $sessionId)
    {
        parent::__construct(sprintf('Корзина пуста для сессии: %s', $sessionId));
    }
}
