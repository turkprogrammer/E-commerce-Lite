<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Недостаточно товара на складе
 */
class InsufficientStockException extends DomainException
{
    public function __construct(?int $productId, int $requested, int $available)
    {
        parent::__construct(sprintf(
            'Недостаточно товара %s: запрошено %d, доступно %d',
            $productId ?? 'unknown',
            $requested,
            $available
        ));
    }
}
