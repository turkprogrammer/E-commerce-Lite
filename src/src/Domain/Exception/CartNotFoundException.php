<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Корзина не найдена
 */
class CartNotFoundException extends DomainException
{
    public function __construct(string $sessionId)
    {
        parent::__construct(sprintf('Корзина не найдена для сессии: %s', $sessionId));
    }
}
