<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Заказ
 *
 * Сущность заказа, представляющая оформленный заказ клиента с информацией о покупателе,
 * адресе доставки, статусе и списком товаров. Связана с элементами заказа через коллекцию.
 */
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Номер заказа
     *
     * @var string Уникальный номер заказа, генерируется автоматически при создании
     */
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $orderNumber;

    /**
     * Имя клиента
     *
     * @var string Имя клиента, оформившего заказ (максимум 200 символов)
     */
    #[ORM\Column(type: 'string', length: 200)]
    private string $customerName;

    /**
     * Email клиента
     *
     * @var string Email клиента для связи (максимум 200 символов)
     */
    #[ORM\Column(type: 'string', length: 200)]
    private string $customerEmail;

    /**
     * Телефон клиента
     *
     * @var string Телефон клиента для связи (максимум 50 символов)
     */
    #[ORM\Column(type: 'string', length: 50)]
    private string $customerPhone;

    /**
     * Адрес доставки
     *
     * @var string Полный адрес доставки заказа
     */
    #[ORM\Column(type: 'text')]
    private string $deliveryAddress;

    /**
     * Статус заказа
     */
    #[ORM\Column(type: 'string', length: 50, enumType: OrderStatus::class)]
    private OrderStatus $status = OrderStatus::Pending;

    /**
     * Общая сумма заказа
     *
     * @var float Общая стоимость всех товаров в заказе
     */
    #[ORM\Column(type: 'float', scale: 2)]
    private float $totalAmount;

    /**
     * Элементы заказа
     *
     * @var Collection<int, OrderItem> Коллекция элементов заказа
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', orphanRemoval: true, cascade: ['persist'])]
    private Collection $items;

    /**
     * Платежи по заказу
     *
     * @var Collection<int, Payment> Коллекция платежей заказа
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'order', orphanRemoval: false)]
    private Collection $payments;

    /**
     * Дата создания заказа
     *
     * @var \DateTimeImmutable Дата и время создания заказа
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Дата обновления заказа
     *
     * @var \DateTimeImmutable Дата и время последнего обновления заказа
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Конструктор класса Order
     *
     * Инициализирует коллекции элементов заказа и платежей, генерирует уникальный номер заказа.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->orderNumber = 'ORD-' . strtoupper(uniqid());
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Получить идентификатор заказа
     *
     * @return int|null Идентификатор заказа или null, если заказ еще не сохранен
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить номер заказа
     *
     * @return string Уникальный номер заказа
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * Получить имя клиента
     *
     * @return string Имя клиента, оформившего заказ
     */
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    /**
     * Установить имя клиента
     *
     * @param string $customerName Имя клиента
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    /**
     * Получить email клиента
     *
     * @return string Email клиента для связи
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * Установить email клиента
     *
     * @param string $customerEmail Email клиента
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * Получить телефон клиента
     *
     * @return string Телефон клиента для связи
     */
    public function getCustomerPhone(): string
    {
        return $this->customerPhone;
    }

    /**
     * Установить телефон клиента
     *
     * @param string $customerPhone Телефон клиента
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setCustomerPhone(string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    /**
     * Получить адрес доставки
     *
     * @return string Полный адрес доставки заказа
     */
    public function getDeliveryAddress(): string
    {
        return $this->deliveryAddress;
    }

    /**
     * Установить адрес доставки
     *
     * @param string $deliveryAddress Адрес доставки
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * Получить статус заказа
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * Установить статус заказа
     */
    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Получить общую сумму заказа
     *
     * @return float Общая стоимость всех товаров в заказе
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * Установить общую сумму заказа
     *
     * @param float $totalAmount Общая стоимость заказа
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    /**
     * Получить элементы заказа
     *
     * @return Collection<int, OrderItem> Коллекция элементов заказа
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Добавить элемент в заказ
     *
     * @param OrderItem $item Элемент заказа для добавления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
        }
        return $this;
    }

    /**
     * Удалить элемент из заказа
     *
     * @param OrderItem $item Элемент заказа для удаления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function removeItem(OrderItem $item): self
    {
        $this->items->removeElement($item);
        return $this;
    }

    /**
     * Пересчитать заказ
     *
     * Обновляет общую сумму заказа на основе стоимости элементов заказа.
     */
    public function recalculate(): void
    {
        $this->totalAmount = 0.0;

        foreach ($this->items as $item) {
            $this->totalAmount += $item->getTotalPrice();
        }
    }

    /**
     * Получить платежи заказа
     *
     * @return Collection<int, Payment> Коллекция платежей заказа
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * Добавить платёж к заказу
     *
     * @param Payment $payment Платёж для добавления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setOrder($this);
        }
        return $this;
    }

    /**
     * Удалить платёж из заказа
     *
     * @param Payment $payment Платёж для удаления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function removePayment(Payment $payment): self
    {
        $this->payments->removeElement($payment);
        return $this;
    }

    /**
     * Получить последний платёж заказа
     */
    public function getLastPayment(): ?Payment
    {
        if ($this->payments->isEmpty()) {
            return null;
        }

        /** @var Payment|false $last */
        $last = $this->payments->last();

        return $last instanceof Payment ? $last : null;
    }

    /**
     * Проверить, оплачен ли заказ
     */
    public function isPaid(): bool
    {
        foreach ($this->payments as $payment) {
            if ($payment->isPaid()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получить дату создания заказа
     *
     * @return \DateTimeImmutable Дата и время создания заказа
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Получить дату обновления заказа
     *
     * @return \DateTimeImmutable Дата и время последнего обновления заказа
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Обновить дату обновления при изменении сущности
     */
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
