<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Ошибка обработки webhook
 */
class WebhookException extends DomainException
{
    public static function unknownType(string $eventType): self
    {
        return new self(sprintf('Неизвестный тип webhook: %s', $eventType));
    }

    public static function invalidPayload(string $reason): self
    {
        return new self(sprintf('Невалидный payload webhook: %s', $reason));
    }
}
