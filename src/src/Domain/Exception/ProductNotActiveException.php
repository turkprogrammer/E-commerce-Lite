<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Товар не активен
 */
class ProductNotActiveException extends DomainException
{
    public function __construct(?int $productId)
    {
        parent::__construct(sprintf('Товар не активен: %s', $productId ?? 'unknown'));
    }
}
