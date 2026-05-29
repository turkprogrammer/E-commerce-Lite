<?php

declare(strict_types=1);

namespace App\Application\Cart;

/**
 * Данные для оформления заказа
 */
final readonly class CheckoutData
{
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $deliveryAddress,
    ) {}

    /**
     * Создать из массива
     *
     * @param array{customerName: string, customerEmail: string, customerPhone: string, deliveryAddress: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customerName: (string) ($data['customerName'] ?? ''),
            customerEmail: (string) ($data['customerEmail'] ?? ''),
            customerPhone: (string) ($data['customerPhone'] ?? ''),
            deliveryAddress: (string) ($data['deliveryAddress'] ?? ''),
        );
    }
}
