<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Платёж
 *
 * Сущность платежа, представляющая финансовую транзакцию для заказа.
 * Содержит информацию о сумме, статусе и деталях оплаты.
 */
#[ORM\Entity]
#[ORM\Table(name: 'payments')]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Номер платежа
     *
     * @var string Уникальный номер платежа, генерируется автоматически
     */
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $paymentNumber;

    /**
     * Сумма платежа
     *
     * @var float Сумма, которая была оплачена
     */
    #[ORM\Column(type: 'float', scale: 2)]
    private float $amount;

    /**
     * Статус платежа
     *
     * @var string Текущий статус платежа (pending, paid, failed, refunded)
     */
    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'pending';

    /**
     * Метод оплаты
     *
     * @var string Метод оплаты, использованный для совершения платежа
     */
    #[ORM\Column(type: 'string', length: 50)]
    private string $method;

    /**
     * Дата создания платежа
     *
     * @var \DateTimeImmutable Дата и время создания платежа
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Дата обновления платежа
     *
     * @var \DateTimeImmutable Дата и время последнего обновления статуса платежа
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Заказ, связанный с этим платежом
     *
     * @var Order|null Ссылка на заказ, для которого был совершен платеж
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    private ?Order $order = null;

    /**
     * Дополнительные данные платежа
     *
     * @var array Дополнительная информация о платеже в формате JSON
     */
    #[ORM\Column(type: 'json', options: ['default' => '{}'])]
    /** @var array<string, mixed> */
    private array $metadata = [];

    /**
     * Конструктор класса Payment
     *
     * Инициализирует даты создания и обновления, генерирует уникальный номер платежа.
     */
    public function __construct()
    {
        $this->paymentNumber = 'PAY-' . strtoupper(uniqid());
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Получить идентификатор платежа
     *
     * @return int|null Идентификатор платежа или null, если платеж еще не сохранен
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить номер платежа
     *
     * @return string Уникальный номер платежа
     */
    public function getPaymentNumber(): string
    {
        return $this->paymentNumber;
    }

    /**
     * Установить номер платежа
     *
     * @param string $paymentNumber Номер платежа
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setPaymentNumber(string $paymentNumber): self
    {
        $this->paymentNumber = $paymentNumber;
        return $this;
    }

    /**
     * Получить сумму платежа
     *
     * @return float Сумма, которая была оплачена
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Установить сумму платежа
     *
     * @param float $amount Сумма платежа
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Получить статус платежа
     *
     * @return string Текущий статус платежа
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Установить статус платежа
     *
     * @param string $status Новый статус платежа
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить метод оплаты
     *
     * @return string Метод оплаты, использованный для совершения платежа
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Установить метод оплаты
     *
     * @param string $method Метод оплаты
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Получить дату создания платежа
     *
     * @return \DateTimeImmutable Дата и время создания платежа
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Получить дату обновления платежа
     *
     * @return \DateTimeImmutable Дата и время последнего обновления статуса платежа
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Получить заказ, связанный с этим платежом
     *
     * @return Order|null Ссылка на заказ, для которого был совершен платеж
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Установить заказ для этого платежа
     *
     * @param Order|null $order Заказ, для которого будет совершен платеж
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Получить дополнительные данные платежа
     *
     * @return array<string, mixed> Дополнительная информация о платеже
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Установить дополнительные данные платежа
     *
     * @param array<string, mixed> $metadata Дополнительная информация о платеже
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Проверить, успешно ли оплачен платеж
     *
     * @return bool Возвращает true, если платеж успешно оплачен, иначе false
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Проверить, находится ли платеж в ожидании
     *
     * @return bool Возвращает true, если платеж в ожидании, иначе false
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Проверить, неудачен ли платеж
     *
     * @return bool Возвращает true, если платеж неудачен, иначе false
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Проверить, возвращен ли платеж
     *
     * @return bool Возвращает true, если платеж возвращен, иначе false
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}
