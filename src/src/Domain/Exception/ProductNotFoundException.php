<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Товар не найден
 */
class ProductNotFoundException extends DomainException
{
    public function __construct(?int $productId)
    {
        parent::__construct(sprintf('Товар не найден: %s', $productId ?? 'unknown'));
    }
}
