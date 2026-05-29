<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Статус заказа
 */
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    /**
     * Получить человекочитаемую метку
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает оплаты',
            self::Paid => 'Оплачен',
            self::Confirmed => 'Подтверждён',
            self::Shipped => 'Отправлен',
            self::Delivered => 'Доставлен',
            self::Cancelled => 'Отменён',
        };
    }
}
