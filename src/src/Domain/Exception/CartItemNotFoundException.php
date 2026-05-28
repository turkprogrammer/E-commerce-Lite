<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Товар не найден в корзине
 */
class CartItemNotFoundException extends DomainException
{
    public function __construct(int $productId)
    {
        parent::__construct(sprintf('Товар не найден в корзине: %d', $productId));
    }
}
